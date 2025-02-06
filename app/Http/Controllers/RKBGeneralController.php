<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use Illuminate\Validation\Rule;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RKBGeneralController extends Controller
{
    public function index ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = RKB::query ()
            ->with ( [ 'proyek', 'linkAlatDetailRkbs' ] )
            ->where ( 'tipe', 'general' );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nomor', 'ilike', "%{$search}%" ) // Menggunakan ilike untuk case-insensitive
                    ->orWhereHas ( 'proyek', function ($query) use ($search)
                    {
                        $query->where ( 'nama', 'ilike', "%{$search}%" );
                    } )
                    ->orWhere ( function ($q) use ($search)
                    {
                        // Handle year search (4 digits)
                        if ( preg_match ( '/^[0-9]{4}$/', $search ) )
                        {
                            $q->whereRaw ( 'EXTRACT(YEAR FROM periode) = ?', [ $search ] ); // PostgreSQL syntax
                        }
                        // Handle month name in Indonesian or English
                        elseif ( $this->isMonthName ( $search ) )
                        {
                            $monthNumber = $this->getMonthNumber ( $search );
                            if ( $monthNumber )
                            {
                                $q->whereRaw ( 'EXTRACT(MONTH FROM periode) = ?', [ $monthNumber ] ); // PostgreSQL syntax
                            }
                        }
                        // Handle "Month Year" format
                        elseif ( preg_match ( '/^([A-Za-z]+)\s+([0-9]{4})$/', $search, $matches ) )
                        {
                            $monthNumber = $this->getMonthNumber ( $matches[ 1 ] );
                            if ( $monthNumber )
                            {
                                $q->whereRaw (
                                    'EXTRACT(MONTH FROM periode) = ? AND EXTRACT(YEAR FROM periode) = ?',
                                    [ $monthNumber, $matches[ 2 ] ]
                                ); // PostgreSQL syntax
                            }
                        }
                    } )
                    ->orWhere ( function ($q) use ($search)
                    {
                        // Handle status search
                        $statusKeywords = [ 'pengajuan', 'evaluasi', 'disetujui', 'tidak diketahui' ];
                        if ( in_array ( strtolower ( $search ), $statusKeywords ) )
                        {
                            $this->getStatusQuery ( $q, $search );
                        }
                    } );
            } );
        }

        $TableData = $query
            ->orderBy ( 'periode', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Filter projects based on user role
        $user         = Auth::user ();
        $proyeksQuery = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        $proyeks = $proyeksQuery
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        return view ( 'dashboard.rkb.general.general', [ 
            'headerPage' => 'RKB General',
            'page'       => 'Data RKB General',
            'proyeks'    => $proyeks,
            'TableData'  => $TableData,
        ] );
    }

    /**
     * Check if the given string is a month name
     */
    private function isMonthName ( $string )
    {
        $months = array_merge (
            // Indonesian month names
            [ 
                'januari',
                'februari',
                'maret',
                'april',
                'mei',
                'juni',
                'juli',
                'agustus',
                'september',
                'oktober',
                'november',
                'desember'
            ],
            // English month names
            [ 
                'january',
                'february',
                'march',
                'april',
                'may',
                'june',
                'july',
                'august',
                'september',
                'october',
                'november',
                'december'
            ]
        );

        return in_array ( strtolower ( $string ), $months );
    }

    /**
     * Get month number from month name
     */
    private function getMonthNumber ( $monthName )
    {
        $monthMap = [ 
            // Indonesian
            'januari'   => 1,
            'februari'  => 2,
            'maret'     => 3,
            'april'     => 4,
            'mei'       => 5,
            'juni'      => 6,
            'juli'      => 7,
            'agustus'   => 8,
            'september' => 9,
            'oktober'   => 10,
            'november'  => 11,
            'desember'  => 12,
            // English
            'january'   => 1,
            'february'  => 2,
            'march'     => 3,
            'april'     => 4,
            'may'       => 5,
            'june'      => 6,
            'july'      => 7,
            'august'    => 8,
            'september' => 9,
            'october'   => 10,
            'november'  => 11,
            'december'  => 12
        ];

        return $monthMap[ strtolower ( $monthName ) ] ?? null;
    }

    private function getStatusQuery ( $query, $status )
    {
        return match ( strtolower ( $status ) )
        {
            'pengajuan' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', false )
                    ->where ( 'is_evaluated', false )
                    ->where ( 'is_approved_vp', false )
                    ->where ( 'is_approved_svp', false );
                } ),
            'evaluasi' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', true )
                    ->where ( 'is_approved_svp', false );
                } ),
            'disetujui' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', true )
                    ->where ( 'is_evaluated', true )
                    ->where ( 'is_approved_vp', true )
                    ->where ( 'is_approved_svp', true );
                } ),
            'tidak diketahui' => $query->where ( function ($q)
                {
                    $q->whereNotIn ( 'id', function ($sub)
                    {
                        $sub->select ( 'id' )
                        ->from ( 'rkb' )
                        ->where ( function ($q1)
                        {
                            // Pengajuan condition
                            $q1->where ( 'is_finalized', false )
                            ->where ( 'is_evaluated', false )
                            ->where ( 'is_approved_vp', false )
                            ->where ( 'is_approved_svp', false );
                        } )
                        ->orWhere ( function ($q2)
                        {
                            // Evaluasi condition
                            $q2->where ( 'is_finalized', true )
                            ->where ( 'is_approved_svp', false );
                        } )
                        ->orWhere ( function ($q3)
                        {
                            // Disetujui condition
                            $q3->where ( 'is_finalized', true )
                            ->where ( 'is_evaluated', true )
                            ->where ( 'is_approved_vp', true )
                            ->where ( 'is_approved_svp', true );
                        } );
                    } );
                } ),
            default => $query
        };
    }

    public function show ( $id )
    {
        // Gunakan eager loading untuk relasi terkait
        $rkb = RKB::with ( [ 
            'proyek', // Relasi ke Proyek
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart',
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart'
        ] )->find ( $id );

        // Cek apakah RKB ditemukan
        if ( ! $rkb )
        {
            return response ()->json ( [ 
                'success' => false,
                'message' => 'RKB Tidak Ditemukan.'
            ], 404 );
        }

        // Format data untuk respons JSON
        $formattedData = [ 
            'id'      => $rkb->id,
            'nomor'   => $rkb->nomor,
            'periode' => Carbon::parse ( $rkb->periode )->format ( 'Y-m' ), // Format periode ke 'YYYY-MM'
            'proyek'  => [ 
                'id'   => $rkb->proyek->id ?? null,
                'nama' => $rkb->proyek->nama ?? '-'
            ],
            'details' => $rkb->linkAlatDetailRkbs->flatMap ( function ($linkAlat)
            {
                return $linkAlat->linkRkbDetails->map ( function ($detail)
                {
                    $general = $detail->detailRkbGeneral;
                    return [ 
                        'quantity_requested' => $general->quantity_requested ?? null,
                        'quantity_approved'  => $general->quantity_approved ?? null,
                        'satuan'             => $general->satuan ?? null,
                        'kategori_sparepart' => [ 
                            'id'   => $general->kategoriSparepart->id ?? null,
                            'nama' => $general->kategoriSparepart->nama ?? '-',
                        ],
                        'sparepart'          => [ 
                            'id'          => $general->masterDataSparepart->id ?? null,
                            'nama'        => $general->masterDataSparepart->nama ?? '-',
                            'part_number' => $general->masterDataSparepart->part_number ?? null,
                        ]
                    ];
                } );
            } )
        ];

        return response ()->json ( [ 
            'success' => true,
            'data'    => $formattedData
        ] );
    }

    public function store ( Request $request )
    {
        // Validasi data request
        $validatedData = $request->validate ( [ 
            'nomor'   => [ 'required', 'string', 'max:255', 'unique:rkb,nomor' ],
            'periode' => [ 'required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/' ], // Validasi periode dalam format YYYY-MM
            'proyek'  => [ 'required', 'integer', 'exists:proyek,id' ], // Validasi proyek ID
        ] );

        // Tambahkan hari default (26) agar sesuai dengan tipe DATE di database
        $validatedData[ 'periode' ] = $validatedData[ 'periode' ] . '-26';

        // Pastikan kolom 'proyek' dipetakan ke 'id_proyek'
        $validatedData[ 'id_proyek' ] = $validatedData[ 'proyek' ];
        unset ( $validatedData[ 'proyek' ] ); // Hapus field 'proyek' karena tidak ada di tabel

        // Set default tipe ke 'General'
        $validatedData[ 'tipe' ] = 'general';

        // Simpan data ke tabel RKB
        RKB::create ( $validatedData );

        // Redirect dengan pesan sukses
        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB General successfully created' );
    }

    public function update ( Request $request, $id )
    {
        $rkb = RKB::find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_general.index' )->with ( 'error', 'RKB not found' );
        }

        // Modify the request to map 'proyek' to 'id_proyek'
        $request->merge ( [ 'id_proyek' => $request->proyek ] );

        // Validate the request
        $validatedData = $request->validate ( [ 
            'nomor'     => [ 'sometimes', 'required', 'string', 'max:255', Rule::unique ( 'rkb', 'nomor' )->ignore ( $rkb->id ) ],
            'periode'   => [ 'sometimes', 'required', 'date' ],
            'id_proyek' => [ 'sometimes', 'required', 'integer', 'exists:proyek,id' ],
        ] );

        // Update the RKB record
        $rkb->update ( $validatedData );

        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully updated' );
    }

    public function destroy ( $id )
    {
        $rkb = RKB::find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_general.index' )->with ( 'error', 'RKB not found' );
        }

        $rkb->delete ();

        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully deleted' );
    }

    public function finalize ( $id )
    {
        // check first if the RKB have link_alat_detail_rkb or not
        $rkb = RKB::findOrFail ( $id );
        if ( isset ( $rkb->linkAlatDetailRkbs ) )
        {
            if ( $rkb->linkAlatDetailRkbs->count () > 0 )
            {

                $rkb = RKB::find ( $id );

                if ( ! $rkb )
                {
                    return redirect ()->route ( 'rkb_general.index' )->with ( 'error', 'RKB not found' );
                }

                $rkb->is_finalized = true;
                $rkb->save ();

                return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully finalized' );
            }
        }
        return redirect ()->back ()->with ( 'error', 'Anda belum mengisi data detail RKB' );
    }
}
