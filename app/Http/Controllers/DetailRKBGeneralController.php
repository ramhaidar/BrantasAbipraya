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
use Illuminate\Support\Facades\Auth;

class DetailRKBGeneralController extends Controller
{
    public function index ( Request $request, $id )
    {
        if ( $request->get ( 'per_page' ) != -1 )
        {
            $parameters               = $request->except ( 'per_page' );
            $parameters[ 'per_page' ] = -1;

            return redirect ()->route (
                'evaluasi_rkb_general.detail.index',
                array_merge ( [ 'id' => $id ], $parameters )
            );
        }

        $perPage = (int) $request->per_page;

        $rkb = RKB::with ( [ 'proyek' ] )->find ( $id );

        // Modified query with proper joins
        $query = DetailRKBGeneral::query ()
            ->join ( 'link_rkb_detail', 'detail_rkb_general.id', '=', 'link_rkb_detail.id_detail_rkb_general' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_general.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_general.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $id )
            ->select ( [ 
                'detail_rkb_general.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk'
            ] );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'detail_rkb_general.satuan', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.jenis_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.kode_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.kode', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.part_number', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.merk', 'like', "%{$search}%" );
            } );
        }

        $available_alat = MasterDataAlat::whereHas ( 'alatProyek', function ($query) use ($rkb)
        {
            $query->where ( 'id_proyek', $rkb->id_proyek )
                ->whereNull ( 'removed_at' );
        } )->get ();

        // Modified TableData to include ordering
        $TableData = $perPage === -1
            ? $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $query->count () )
            : $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $perPage );

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        return view ( 'dashboard.rkb.general.detail.detail', [ 
            'headerPage'            => "RKB General",
            'page'                  => 'Detail RKB General [' . $rkb->proyek->nama . ' | ' . $rkb->nomor . ']',

            'proyeks'               => $proyeks,
            'rkb'                   => $rkb,
            'available_alat'        => $available_alat,
            'master_data_sparepart' => MasterDataSparepart::all (),
            'kategori_sparepart'    => KategoriSparepart::all (),
            'TableData'             => $TableData,
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

        // Buat entri baru di DetailRKBGeneral
        $detailRKBGeneral = DetailRkbGeneral::create ( [ 
            'quantity_requested'              => $validatedData[ 'quantity_requested' ],
            'satuan'                          => $validatedData[ 'satuan' ],
            'id_kategori_sparepart_sparepart' => $validatedData[ 'id_kategori_sparepart_sparepart' ],
            'id_master_data_sparepart'        => $validatedData[ 'id_master_data_sparepart' ],
        ] );

        // Buat entri LinkRkbDetail baru terlebih dahulu
        $linkRkbDetail = LinkRkbDetail::create ( [ 
            'id_detail_rkb_general'   => $detailRKBGeneral->id,
            'id_link_alat_detail_rkb' => null, // Temporarily null
        ] );

        // Buat atau cari LinkAlatDetailRkb
        $linkAlatDetailRKB = LinkAlatDetailRkb::firstOrCreate (
            [ 
                'id_rkb'              => $validatedData[ 'id_rkb' ], // Use the newly created LinkRkbDetail id
                'id_master_data_alat' => $validatedData[ 'id_master_data_alat' ],
            ],
            [ 
                'nama_koordinator' => null
            ] // Nilai default jika tidak ditemukan
        );

        // Update the LinkRkbDetail with the correct link_alat_detail_rkb ID
        $linkRkbDetail->update ( [ 
            'id_link_alat_detail_rkb' => $linkAlatDetailRKB->id,
        ] );

        return redirect ()->back ()->with ( 'success', 'Detail RKB General created and linked successfully!' );
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
}
