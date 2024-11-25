<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\LinkRKBDetail;
use App\Models\MasterDataAlat;
use App\Models\DetailRKBGeneral;
use App\Models\KategoriSparepart;
use App\Models\LinkAlatDetailRKB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;

class DetailRKBGeneralController extends Controller
{
    public function index ( $id )
    {
        $rkb                   = RKB::with ( [ 'proyek' ] )->find ( $id );
        $proyeks               = Proyek::orderByDesc ( 'updated_at' )->get ();
        $master_data_alat      = MasterDataAlat::all ();
        $master_data_sparepart = MasterDataSparepart::all ();
        $kategori_sparepart    = KategoriSparepart::all ();

        return view ( 'dashboard.rkb.general.detail.detail', [ 
            'rkb'                   => $rkb,
            'proyeks'               => $proyeks,
            'master_data_alat'      => $master_data_alat,
            'master_data_sparepart' => $master_data_sparepart,
            'kategori_sparepart'    => $kategori_sparepart,

            'headerPage'            => "RKB General",
            'page'                  => 'Detail RKB General',
        ] );
    }

    // Store a new DetailRKBGeneral
    public function store ( Request $request )
    {
        // Validasi input
        $validatedData = $request->validate ( [ 
            'quantity_requested'              => 'required|integer|min:1',
            'satuan'                          => 'required|string|max:50',
            'id_master_data_alat'             => 'required|integer|exists:master_data_alat,id',
            'id_kategori_sparepart_sparepart' => 'required|integer|exists:kategori_sparepart,id',
            'id_master_data_sparepart'        => 'required|integer|exists:master_data_sparepart,id',
            'id_rkb'                          => 'required|integer|exists:rkb,id', // Pastikan RKB terkait
        ] );

        try
        {
            // Buat entri baru di DetailRKBGeneral
            $detailRKBGeneral = DetailRkbGeneral::create ( [ 
                'quantity_requested'              => $validatedData[ 'quantity_requested' ],
                'satuan'                          => $validatedData[ 'satuan' ],
                'id_kategori_sparepart_sparepart' => $validatedData[ 'id_kategori_sparepart_sparepart' ],
                'id_master_data_sparepart'        => $validatedData[ 'id_master_data_sparepart' ],
            ] );

            // Cari atau buat LinkAlatDetailRkb
            $linkAlatDetailRKB = LinkAlatDetailRkb::firstOrCreate (
                [ 
                    'id_rkb'              => $validatedData[ 'id_rkb' ],
                    'id_master_data_alat' => $validatedData[ 'id_master_data_alat' ],
                ],
                [ 
                    'id_timeline_rkb_urgent' => null, // Default null, bisa diupdate nanti
                ]
            );

            // Buat LinkRkbDetail baru
            $linkAlatDetailRKB = LinkRkbDetail::create (
                [ 
                    'id_detail_rkb_general'   => $detailRKBGeneral->id,
                    'id_link_alat_detail_rkb' => $linkAlatDetailRKB->id,
                ]
            );

            return redirect ()->back ()->with ( 'success', 'Detail RKB General created and linked successfully!' );
        }
        catch ( \Exception $e )
        {
            // Tangani kesalahan dan kembalikan pesan kesalahan ke pengguna
            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Failed to create Detail RKB General: ' . $e->getMessage (),
            ] );
        }
    }


    // Return the data in json for a specific DetailRKBGeneral
    public function show ( $id )
    {
        // Ambil data DetailRKBGeneral dengan relasi terkait
        $detailRKBGeneral = DetailRKBGeneral::with ( [ 
            'kategoriSparepart:id,kode,nama',
            'masterDataSparepart:id,nama,part_number,merk',
            'linkRkbDetails.linkAlatDetailRkb.masterDataAlat:id,jenis_alat'
        ] )->find ( $id );

        if ( ! $detailRKBGeneral )
        {
            return response ()->json ( [ 
                'error' => 'Detail RKB General not found!',
            ], 404 );
        }

        // Format respons
        return response ()->json ( [ 
            'data' => [ 
                'id'                              => $detailRKBGeneral->id,
                'id_master_data_alat'             => optional ( $detailRKBGeneral->linkRkbDetails->first ()->linkAlatDetailRkb->masterDataAlat )->id,
                'id_kategori_sparepart_sparepart' => $detailRKBGeneral->id_kategori_sparepart_sparepart,
                'id_master_data_sparepart'        => $detailRKBGeneral->id_master_data_sparepart,
                'quantity_requested'              => $detailRKBGeneral->quantity_requested,
                'satuan'                          => $detailRKBGeneral->satuan,
                'master_data_sparepart'           => [ 
                    'id'      => $detailRKBGeneral->masterDataSparepart->id ?? null,
                    'name'    => $detailRKBGeneral->masterDataSparepart->nama ?? null,
                    'details' => $detailRKBGeneral->masterDataSparepart
                        ? "{$detailRKBGeneral->masterDataSparepart->nama} - {$detailRKBGeneral->masterDataSparepart->part_number} - {$detailRKBGeneral->masterDataSparepart->merk}"
                        : null,
                ],
            ]
        ] );
    }



    // Update an existing DetailRKBGeneral
    public function update ( Request $request, $id )
    {
        // Cari data DetailRKBGeneral
        $detailRKBGeneral = DetailRKBGeneral::find ( $id );

        if ( ! $detailRKBGeneral )
        {
            return redirect ()->back ()->with ( 'error', 'Detail RKB General not found!' );
        }

        // Validasi input
        $validatedData = $request->validate ( [ 
            'quantity_requested'              => 'required|integer|min:1',
            'satuan'                          => 'required|string|max:50',
            'id_master_data_alat'             => 'required|integer|exists:master_data_alat,id',
            'id_kategori_sparepart_sparepart' => 'required|integer|exists:kategori_sparepart,id',
            'id_master_data_sparepart'        => 'required|integer|exists:master_data_sparepart,id',
            'id_rkb'                          => 'required|integer|exists:rkb,id', // Pastikan RKB terkait
        ] );

        try
        {
            // Update DetailRKBGeneral
            $detailRKBGeneral->update ( [ 
                'quantity_requested'              => $validatedData[ 'quantity_requested' ],
                'satuan'                          => $validatedData[ 'satuan' ],
                'id_kategori_sparepart_sparepart' => $validatedData[ 'id_kategori_sparepart_sparepart' ],
                'id_master_data_sparepart'        => $validatedData[ 'id_master_data_sparepart' ],
            ] );

            // Ambil atau buat LinkAlatDetailRkb baru
            $newLinkAlatDetailRKB = LinkAlatDetailRkb::firstOrCreate (
                [ 
                    'id_rkb'              => $validatedData[ 'id_rkb' ],
                    'id_master_data_alat' => $validatedData[ 'id_master_data_alat' ],
                ],
                [ 
                    'id_timeline_rkb_urgent' => null, // Default null
                ]
            );

            // Ambil LinkRKBDetail lama
            $currentLinkRkbDetail = LinkRKBDetail::where ( 'id_detail_rkb_general', $id )->first ();

            if ( $currentLinkRkbDetail )
            {
                $currentLinkAlatDetailRKBId = $currentLinkRkbDetail->id_link_alat_detail_rkb;

                // Update LinkRKBDetail dengan LinkAlatDetailRkb baru
                $currentLinkRkbDetail->update ( [ 
                    'id_link_alat_detail_rkb' => $newLinkAlatDetailRKB->id,
                ] );

                // Periksa apakah LinkAlatDetailRKB lama masih digunakan
                $remainingLinks = LinkRKBDetail::where ( 'id_link_alat_detail_rkb', $currentLinkAlatDetailRKBId )->exists ();

                // Hapus LinkAlatDetailRKB lama jika tidak ada lagi relasi
                if ( ! $remainingLinks )
                {
                    LinkAlatDetailRKB::where ( 'id', $currentLinkAlatDetailRKBId )->delete ();
                }
            }
            else
            {
                // Jika belum ada, buat LinkRKBDetail baru
                LinkRKBDetail::create ( [ 
                    'id_detail_rkb_general'   => $detailRKBGeneral->id,
                    'id_link_alat_detail_rkb' => $newLinkAlatDetailRKB->id,
                ] );
            }

            return redirect ()->back ()->with ( 'success', 'Detail RKB General updated successfully!' );
        }
        catch ( \Exception $e )
        {
            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Failed to update Detail RKB General: ' . $e->getMessage (),
            ] );
        }
    }

    // Delete a specific DetailRKBGeneral
    public function destroy ( $id )
    {
        // Cari data DetailRKBGeneral
        $detailRKBGeneral = DetailRKBGeneral::find ( $id );

        if ( ! $detailRKBGeneral )
        {
            return redirect ()->back ()->with ( 'error', 'Detail RKB General not found!' );
        }

        try
        {
            // Dapatkan semua LinkRKBDetail terkait dengan DetailRKBGeneral ini
            $linkRkbDetails = LinkRKBDetail::where ( 'id_detail_rkb_general', $id )->get ();

            // Hapus LinkRKBDetail
            foreach ( $linkRkbDetails as $linkRkbDetail )
            {
                $linkAlatDetailRkbId = $linkRkbDetail->id_link_alat_detail_rkb;

                // Hapus LinkRKBDetail
                $linkRkbDetail->delete ();

                // Cek apakah masih ada LinkRKBDetail yang menggunakan link_alat_detail_rkb ini
                $remainingLinks = LinkRKBDetail::where ( 'id_link_alat_detail_rkb', $linkAlatDetailRkbId )->exists ();

                // Jika tidak ada lagi, hapus link_alat_detail_rkb
                if ( ! $remainingLinks )
                {
                    LinkAlatDetailRKB::where ( 'id', $linkAlatDetailRkbId )->delete ();
                }
            }

            // Hapus DetailRKBGeneral
            $detailRKBGeneral->delete ();

            return redirect ()->back ()->with ( 'success', 'Detail RKB General and its links deleted successfully!' );
        }
        catch ( \Exception $e )
        {
            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Failed to delete Detail RKB General: ' . $e->getMessage (),
            ] );
        }
    }



    public function getData ( Request $request, $id_rkb )
    {
        // Query data dengan relasi
        $query = DetailRKBGeneral::with ( [ 
            'masterDataSparepart:id,nama',
            'kategoriSparepart:id,kode,nama',
            'linkRkbDetails.linkAlatDetailRkb.masterDataAlat:id,jenis_alat' // Load relasi alat
        ] )
            ->whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($q) use ($id_rkb)
            {
                $q->where ( 'id_rkb', $id_rkb );
            } );

        // Filter pencarian
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'quantity_requested', 'like', "%{$search}%" )
                    ->orWhere ( 'quantity_approved', 'like', "%{$search}%" )
                    ->orWhere ( 'satuan', 'like', "%{$search}%" )
                    ->orWhereHas ( 'kategoriSparepart', function ($q) use ($search)
                    {
                        $q->whereRaw ( "CONCAT(kode, ' ', nama) LIKE ?", [ "%{$search}%" ] );
                    } )
                    ->orWhereHas ( 'masterDataSparepart', function ($q) use ($search)
                    {
                        $q->where ( 'nama', 'like', "%{$search}%" );
                    } )
                    ->orWhereHas ( 'linkRkbDetails.linkAlatDetailRkb.masterDataAlat', function ($q) use ($search)
                    {
                        $q->where ( 'jenis_alat', 'like', "%{$search}%" );
                    } );
            } );
        }

        // Sorting
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];

            switch ($columnName)
            {
                case 'masterDataAlat':
                    $query->orderBy ( 'master_data_alat.jenis_alat', $sortDirection );
                    break;
                case 'kategoriSparepart':
                    $query->orderBy ( 'kategori_sparepart.kode', $sortDirection )
                        ->orderBy ( 'kategori_sparepart.nama', $sortDirection );
                    break;
                case 'masterDataSparepart':
                    $query->orderBy ( 'master_data_sparepart.nama', $sortDirection );
                    break;
                default:
                    $query->orderBy ( $columnName, $sortDirection );
                    break;
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

        $totalRecords = DetailRKBGeneral::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($q) use ($id_rkb)
        {
            $q->where ( 'id_rkb', $id_rkb );
        } )->count ();

        $filteredRecords = $query->count ();

        $data = $query->skip ( $start )->take ( $length )->get ()->map ( function ($item)
        {
            return [ 
                'id'                  => $item->id,
                'masterDataAlat'      => $item->linkRkbDetails->first ()->linkAlatDetailRkb->masterDataAlat->jenis_alat ?? '-',
                'kategoriSparepart'   => $item->kategoriSparepart
                    ? "{$item->kategoriSparepart->kode}: {$item->kategoriSparepart->nama}"
                    : '-',
                'masterDataSparepart' => $item->masterDataSparepart->nama ?? '-',
                'quantity_requested'  => $item->quantity_requested,
                'quantity_approved'   => $item->quantity_approved ?? '-',
                'satuan'              => $item->satuan,
                'aksi'                => '',
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
