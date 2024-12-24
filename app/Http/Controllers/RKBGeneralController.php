<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class RKBGeneralController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::orderByDesc ( 'updated_at' )->get ();

        return view ( 'dashboard.rkb.general.general', [ 
            'proyeks'    => $proyeks,

            'headerPage' => "RKB General",
            'page'       => 'Data RKB General',
        ] );
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
        $validatedData[ 'tipe' ] = 'General';

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

    public function getData ( Request $request )
    {
        // Filter hanya tipe "General"
        $query = RKB::with ( 'proyek' )->where ( 'tipe', 'General' );

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
