<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class RKBUrgentController extends Controller
{
    public function index ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = RKB::query ()
            ->with ( [ 'proyek', 'linkAlatDetailRkbs' ] )
            ->where ( 'tipe', 'Urgent' )
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nomor', 'like', "%{$search}%" )
                    ->orWhereHas ( 'proyek', function ($query) use ($search)
                    {
                        $query->where ( 'nama', 'like', "%{$search}%" );
                    } )
                    ->orWhere ( function ($q) use ($search)
                    {
                        // Handle year search (4 digits)
                        if ( preg_match ( '/^[0-9]{4}$/', $search ) )
                        {
                            $q->whereYear ( 'periode', $search );
                        }
                        // Handle month name in Indonesian or English
                        elseif ( $this->isMonthName ( $search ) )
                        {
                            $monthNumber = $this->getMonthNumber ( $search );
                            if ( $monthNumber )
                            {
                                $q->whereMonth ( 'periode', $monthNumber );
                            }
                        }
                        // Handle "Month Year" format (e.g., "January 2023" or "Januari 2023")
                        elseif ( preg_match ( '/^([A-Za-z]+)\s+([0-9]{4})$/', $search, $matches ) )
                        {
                            $monthNumber = $this->getMonthNumber ( $matches[ 1 ] );
                            if ( $monthNumber )
                            {
                                $q->whereMonth ( 'periode', $monthNumber )
                                    ->whereYear ( 'periode', $matches[ 2 ] );
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

        $user = auth ()->user ();

        if ( $user->role === 'Pegawai' )
        {
            $query->whereHas ( 'proyek', function ($q) use ($user)
            {
                $q->whereHas ( 'users', function ($q) use ($user)
                {
                    $q->where ( 'users.id', $user->id );
                } );
            } );
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks       = $user->proyek ()
                ->with ( "users" )
                ->get ();
            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $query->whereHas ( 'proyek', function ($q) use ($usersInProyek)
            {
                $q->whereHas ( 'users', function ($q) use ($usersInProyek)
                {
                    $q->whereIn ( 'users.id', $usersInProyek );
                } );
            } );
        }

        $TableData = $query->paginate ( $perPage )
            ->withQueryString ();

        $proyeks = [];
        if ( $user->role !== 'Pegawai' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();
        }

        return view ( 'dashboard.rkb.urgent.urgent', [ 
            'headerPage'  => 'RKB Urgent',
            'page'        => 'Data RKB Urgent',

            'proyeks'     => $proyeks,
            'TableData'   => $TableData,

            'menuContext' => 'rkb_urgent',  // Add this flag to indicate that Urgent is active
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
                    ->where ( 'is_evaluated', false )
                    ->where ( 'is_approved_vp', false )
                    ->where ( 'is_approved_svp', false );
                } ),
            'disetujui' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', true )
                    ->where ( 'is_evaluated', true )
                    ->where ( 'is_approved_vp', true )
                    ->where ( 'is_approved_svp', true );
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
                    $urgent = $detail->detailRkbGeneral;
                    return [ 
                        'quantity_requested' => $urgent->quantity_requested ?? null,
                        'quantity_approved'  => $urgent->quantity_approved ?? null,
                        'satuan'             => $urgent->satuan ?? null,
                        'kategori_sparepart' => [ 
                            'id'   => $urgent->kategoriSparepart->id ?? null,
                            'nama' => $urgent->kategoriSparepart->nama ?? '-',
                        ],
                        'sparepart'          => [ 
                            'id'          => $urgent->masterDataSparepart->id ?? null,
                            'nama'        => $urgent->masterDataSparepart->nama ?? '-',
                            'part_number' => $urgent->masterDataSparepart->part_number ?? null,
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
            'nomor'   => [ 'required', 'string', 'max:255', 'unique:rkb,nomor' ], // Validasi nomor unik
            'periode' => [ 'required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/' ], // Validasi periode dalam format YYYY-MM
            'proyek'  => [ 'required', 'integer', 'exists:proyek,id' ], // Validasi proyek ID
        ] );

        // Tambahkan hari default (26) agar sesuai dengan tipe DATE di database
        $validatedData[ 'periode' ] = $validatedData[ 'periode' ] . '-26';

        // Pastikan kolom 'proyek' dipetakan ke 'id_proyek'
        $validatedData[ 'id_proyek' ] = $validatedData[ 'proyek' ];
        unset ( $validatedData[ 'proyek' ] ); // Hapus field 'proyek' karena tidak ada di tabel

        // Set default tipe ke 'General'
        $validatedData[ 'tipe' ] = 'Urgent';

        // Simpan data ke tabel RKB
        RKB::create ( $validatedData );

        // Redirect dengan pesan sukses
        return redirect ()->route ( 'rkb_urgent.index' )->with ( 'success', 'RKB General successfully created' );
    }

    public function update ( Request $request, $id )
    {
        $rkb = RKB::find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_urgent.index' )->with ( 'error', 'RKB not found' );
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

        return redirect ()->route ( 'rkb_urgent.index' )->with ( 'success', 'RKB successfully updated' );
    }

    public function destroy ( $id )
    {
        $rkb = RKB::find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_urgent.index' )->with ( 'error', 'RKB not found' );
        }

        $rkb->delete ();

        return redirect ()->route ( 'rkb_urgent.index' )->with ( 'success', 'RKB successfully deleted' );
    }

    public function finalize ( $id )
    {
        // Ambil RKB beserta relasi-relasi yang diperlukan
        $rkb = RKB::with (
            'linkAlatDetailRkbs.timelineRkbUrgents',
            'linkAlatDetailRkbs.lampiranRkbUrgent'
        )->findOrFail ( $id );

        // Validasi jika RKB tidak memiliki linkAlatDetailRkbs
        if ( ! isset ( $rkb->linkAlatDetailRkbs ) || $rkb->linkAlatDetailRkbs->isEmpty () )
        {
            return redirect ()->back ()->with ( 'error', 'Anda belum mengisi Data Detail RKB.' );
        }

        // Iterasi setiap linkAlatDetailRkbs untuk memastikan semua data terisi
        foreach ( $rkb->linkAlatDetailRkbs as $detail )
        {
            // Cek jika timelineRkbUrgents tidak diisi
            if ( ! isset ( $detail->timelineRkbUrgents ) || $detail->timelineRkbUrgents->isEmpty () )
            {
                return redirect ()->back ()->with ( 'error', "Detail RKB dengan Kode Alat {$detail->masterDataAlat->kode_alat} belum memiliki Data Timeline RKB." );
            }

            // Cek jika lampiranRkbUrgent tidak diisi
            if ( ! isset ( $detail->lampiranRkbUrgent ) )
            {
                return redirect ()->back ()->with ( 'error', "Detail RKB dengan Kode Alat {$detail->masterDataAlat->kode_alat} belum memiliki Data Lampiran RKB." );
            }
        }

        // Jika semua validasi lolos, finalisasi RKB
        $rkb->is_finalized = true;
        $rkb->save ();

        return redirect ()->route ( 'rkb_urgent.index' )->with ( 'success', 'RKB berhasil difinalisasi.' );
    }



    public function getData ( Request $request )
    {
        // Filter hanya tipe "General"
        $query = RKB::with ( 'proyek' )->where ( 'tipe', 'Urgent' );

        // Filter pencarian
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nomor', 'like', "%{$search}%" )
                    ->orWhereHas ( 'proyek', function ($q) use ($search)
                    {
                        $q->where ( 'nama', 'like', "%{$search}%" );
                    } )
                    ->orWhere ( 'periode', 'like', "%{$search}%" )
                    ->orWhereRaw ( "CASE 
                    WHEN is_finalized = 1 AND is_approved = 1 THEN 'Disetujui'
                    WHEN is_finalized = 0 THEN 'Pengajuan'
                    ELSE 'Evaluasi' 
                END LIKE ?", [ "%{$search}%" ] );
            } );
        }

        // Sorting
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];

            if ( in_array ( $columnName, [ 'nomor', 'periode' ] ) )
            {
                $query->orderBy ( $columnName, $sortDirection );
            }
            elseif ( $columnName === 'proyek' )
            {
                $query->join ( 'proyek', 'rkb.id_proyek', '=', 'proyek.id' )
                    ->orderBy ( 'proyek.nama', $sortDirection );
            }
        }
        else
        {
            $query->orderBy ( 'updated_at', 'desc' );
        }

        // Pagination
        $draw   = $request->input ( 'draw' );
        $start  = $request->input ( 'start', 0 );
        $length = $request->input ( 'length', 10 );

        $totalRecords    = RKB::where ( 'tipe', 'General' )->count (); // Hanya hitung yang tipe General
        $filteredRecords = $query->count ();

        $rkbData = $query->skip ( $start )->take ( $length )->get ();

        // Mapping data
        $data = $rkbData->map ( function ($item)
        {
            $isFinalized = $item->is_finalized ?? false;
            $isApproved = $item->is_approved ?? false;

            $status = match ( true )
            {
                $isFinalized && $isApproved => 'Disetujui',
                ! $isFinalized => 'Pengajuan',
                default => 'Evaluasi',
            };

            return [ 
                'id'           => $item->id,
                'nomor'        => $item->nomor,
                'proyek'       => $item->proyek->nama ?? '-',
                'periode'      => Carbon::parse ( $item->periode )->translatedFormat ( 'F Y' ),
                'status'       => $status,
                'is_finalized' => $item->is_finalized,
                'is_approved'  => $item->is_approved,
                'is_evaluated' => $item->is_evaluated,
            ];
        } );

        return response ()->json ( [ 
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ] );
    }

}
