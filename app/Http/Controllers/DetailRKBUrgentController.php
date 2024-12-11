<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\LinkRKBDetail;
use App\Models\MasterDataAlat;
use App\Models\DetailRKBUrgent;
use App\Models\KategoriSparepart;
use App\Models\LinkAlatDetailRKB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DetailRKBUrgentController extends Controller
{
    public function index ( $id )
    {
        $rkb                   = RKB::with ( [ 'proyek' ] )->find ( $id );
        $proyeks               = Proyek::orderByDesc ( 'updated_at' )->get ();
        $master_data_alat      = MasterDataAlat::all ();
        $master_data_sparepart = MasterDataSparepart::all ();
        $kategori_sparepart    = KategoriSparepart::all ();
        $data                  = RKB::where ( "tipe", "Urgent" )->with (
            "linkAlatDetailRkbs.rkb",
            "linkAlatDetailRkbs.masterDataAlat",
            // "linkAlatDetailRkbs.timelineRkbUrgent",
            "linkAlatDetailRkbs.linkRkbDetails"
        )->findOrFail ( $id );

        // sort linkAlatDetailRkbs berdasarkan id dari master_data_alat
        $data->linkAlatDetailRkbs = $data->linkAlatDetailRkbs->sortBy ( 'id_master_data_alat' );

        // dd ( $data->toArray () );

        return view ( 'dashboard.rkb.urgent.detail.detail', [ 
            'rkb'                   => $rkb,
            'proyeks'               => $proyeks,
            'master_data_alat'      => $master_data_alat,
            'master_data_sparepart' => $master_data_sparepart,
            'kategori_sparepart'    => $kategori_sparepart,
            'data'                  => $data,

            'headerPage'            => "RKB Urgent",
            'page'                  => 'Detail RKB Urgent',
        ] );
    }

    // Store a new DetailRKBUrgent
    public function store ( Request $request )
    {
        $validatedData = $request->validate ( [ 
            'quantity_requested'              => 'required|integer|min:1',
            'satuan'                          => 'required|string|max:50',
            'nama_mekanik'                    => 'required|string|max:50',
            'kronologi'                       => 'required|string|max:1000',
            'id_master_data_alat'             => 'required|integer|exists:master_data_alat,id',
            'id_kategori_sparepart_sparepart' => 'required|integer|exists:kategori_sparepart,id',
            'id_master_data_sparepart'        => 'required|integer|exists:master_data_sparepart,id',
            'id_rkb'                          => 'required|integer|exists:rkb,id',
            'dokumentasi.*'                   => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
        ] );

        try
        {
            // Ambil nomor RKB
            $rkb       = RKB::findOrFail ( $validatedData[ 'id_rkb' ] );
            $rkbNumber = $rkb->nomor;

            // Buat entri baru di DetailRKBUrgent (tanpa dokumentasi untuk sementara)
            $detailRKBUrgent = DetailRkbUrgent::create ( [ 
                'quantity_requested'              => $validatedData[ 'quantity_requested' ],
                'satuan'                          => $validatedData[ 'satuan' ],
                'nama_mekanik'                    => $validatedData[ 'nama_mekanik' ],
                'kronologi'                       => $validatedData[ 'kronologi' ],
                'dokumentasi'                     => null, // Akan diisi nanti
                'id_kategori_sparepart_sparepart' => $validatedData[ 'id_kategori_sparepart_sparepart' ],
                'id_master_data_sparepart'        => $validatedData[ 'id_master_data_sparepart' ],
            ] );

            // Tentukan folder berdasarkan nomor RKB dan ID DetailRKBUrgent
            $folderPath = "uploads/dokumentasi/{$rkbNumber}/{$detailRKBUrgent->id}/";

            // Tangani unggahan file dokumentasi
            if ( $request->hasFile ( 'dokumentasi' ) )
            {
                foreach ( $request->file ( 'dokumentasi' ) as $file )
                {
                    // Format nama file
                    $originalName = pathinfo ( $file->getClientOriginalName (), PATHINFO_FILENAME );
                    $extension    = $file->getClientOriginalExtension ();
                    $timestamp    = now ()->format ( 'Y-m-d--H-i-s' );
                    $fileName     = "{$originalName}---{$timestamp}.{$extension}";

                    // Simpan file ke folder
                    $file->storeAs ( $folderPath, $fileName, 'public' );
                }
            }

            // Update path folder dokumentasi di DetailRKBUrgent
            $detailRKBUrgent->update ( [ 'dokumentasi' => $folderPath ] );

            // Cari atau buat LinkAlatDetailRkb
            $linkAlatDetailRKB = LinkAlatDetailRkb::firstOrCreate (
                [ 
                    'id_rkb'              => $validatedData[ 'id_rkb' ],
                    'id_master_data_alat' => $validatedData[ 'id_master_data_alat' ],
                ],
                [ 
                    'nama_mekanik' => $validatedData[ 'nama_mekanik' ], // Atur nilai default
                ]
            );

            // Buat LinkRkbDetail baru
            LinkRkbDetail::create ( [ 
                'id_detail_rkb_urgent'    => $detailRKBUrgent->id,
                'id_link_alat_detail_rkb' => $linkAlatDetailRKB->id,
            ] );

            return redirect ()->back ()->with ( 'success', 'Detail RKB Urgent created and linked successfully!' );
        }
        catch ( \Exception $e )
        {
            // Tangani kesalahan dan log error
            \Log::error ( 'Failed to create Detail RKB Urgent: ' . $e->getMessage (), [ 
                'request_data'   => $request->all (),
                'validated_data' => $validatedData,
            ] );

            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Failed to create Detail RKB Urgent: ' . $e->getMessage (),
            ] );
        }
    }


    // Return the data in json for a specific DetailRKBUrgent
    public function show ( $id )
    {
        // Ambil data DetailRKBUrgent dengan relasi terkait
        $detailRKBUrgent = DetailRKBUrgent::with ( [ 
            'kategoriSparepart:id,kode,nama',
            'masterDataSparepart:id,nama,part_number,merk',
            'linkRkbDetails.linkAlatDetailRkb.masterDataAlat:id,jenis_alat'
        ] )->find ( $id );

        if ( ! $detailRKBUrgent )
        {
            return response ()->json ( [ 
                'error' => 'Detail RKB Urgent not found!',
            ], 404 );
        }

        // Format respons
        return response ()->json ( [ 
            'data' => [ 
                'id'                              => $detailRKBUrgent->id,
                'id_master_data_alat'             => optional ( $detailRKBUrgent->linkRkbDetails->first ()->linkAlatDetailRkb->masterDataAlat )->id,
                'id_kategori_sparepart_sparepart' => $detailRKBUrgent->id_kategori_sparepart_sparepart,
                'id_master_data_sparepart'        => $detailRKBUrgent->id_master_data_sparepart,
                'quantity_requested'              => $detailRKBUrgent->quantity_requested,
                'satuan'                          => $detailRKBUrgent->satuan,
                'master_data_sparepart'           => [ 
                    'id'      => $detailRKBUrgent->masterDataSparepart->id ?? null,
                    'name'    => $detailRKBUrgent->masterDataSparepart->nama ?? null,
                    'details' => $detailRKBUrgent->masterDataSparepart
                        ? "{$detailRKBUrgent->masterDataSparepart->nama} - {$detailRKBUrgent->masterDataSparepart->part_number} - {$detailRKBUrgent->masterDataSparepart->merk}"
                        : null,
                ],
            ]
        ] );
    }



    // Update an existing DetailRKBUrgent
    public function update ( Request $request, $id )
    {
        // Cari data DetailRKBUrgent
        $detailRKBUrgent = DetailRKBUrgent::find ( $id );

        if ( ! $detailRKBUrgent )
        {
            return redirect ()->back ()->with ( 'error', 'Detail RKB Urgent not found!' );
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
            // Update DetailRKBUrgent
            $detailRKBUrgent->update ( [ 
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
            $currentLinkRkbDetail = LinkRKBDetail::where ( 'id_detail_rkb_urgent', $id )->first ();

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
                    'id_detail_rkb_urgent'    => $detailRKBUrgent->id,
                    'id_link_alat_detail_rkb' => $newLinkAlatDetailRKB->id,
                ] );
            }

            return redirect ()->back ()->with ( 'success', 'Detail RKB Urgent updated successfully!' );
        }
        catch ( \Exception $e )
        {
            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Failed to update Detail RKB Urgent: ' . $e->getMessage (),
            ] );
        }
    }

    // Delete a specific DetailRKBUrgent
    public function destroy ( $id )
    {
        // Cari data DetailRKBUrgent
        $detailRKBUrgent = DetailRKBUrgent::find ( $id );

        if ( ! $detailRKBUrgent )
        {
            return redirect ()->back ()->with ( 'error', 'Detail RKB Urgent not found!' );
        }

        try
        {
            // Dapatkan semua LinkRKBDetail terkait dengan DetailRKBUrgent ini
            $linkRkbDetails = LinkRKBDetail::where ( 'id_detail_rkb_urgent', $id )->get ();

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

            // Hapus DetailRKBUrgent
            $detailRKBUrgent->delete ();

            return redirect ()->back ()->with ( 'success', 'Detail RKB Urgent and its links deleted successfully!' );
        }
        catch ( \Exception $e )
        {
            return redirect ()->back ()->withErrors ( [ 
                'error' => 'Failed to delete Detail RKB Urgent: ' . $e->getMessage (),
            ] );
        }
    }



    public function getData ( Request $request, $id_rkb )
    {
        // Query dengan join untuk mempermudah sorting kolom terkait relasi
        $query = DetailRKBUrgent::query ()
            ->select ( [ 
                'detail_rkb_urgent.id',
                'detail_rkb_urgent.quantity_requested',
                'detail_rkb_urgent.quantity_approved',
                'detail_rkb_urgent.satuan',
                'master_data_alat.jenis_alat as namaAlat', // "Nama Alat" diambil dari jenis_alat
                'master_data_alat.kode_alat as kodeAlat',
                'kategori_sparepart.kode as kodeKategoriSparepart',
                'kategori_sparepart.nama as namaKategoriSparepart',
                'master_data_sparepart.nama as sparepart',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk',
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_urgent.id', '=', 'link_rkb_detail.id_detail_rkb_urgent' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->leftJoin ( 'kategori_sparepart', 'detail_rkb_urgent.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->leftJoin ( 'master_data_sparepart', 'detail_rkb_urgent.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $id_rkb );

        // Filter pencarian
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'master_data_alat.jenis_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.kode_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.kode', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.part_number', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.merk', 'like', "%{$search}%" )
                    ->orWhere ( 'detail_rkb_urgent.satuan', 'like', "%{$search}%" )
                    ->orWhere ( 'detail_rkb_urgent.quantity_requested', 'like', "%{$search}%" )
                    ->orWhere ( 'detail_rkb_urgent.quantity_approved', 'like', "%{$search}%" );
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
                case 'namaAlat':
                    $query->orderBy ( 'master_data_alat.jenis_alat', $sortDirection );
                    break;
                case 'kodeAlat':
                    $query->orderBy ( 'master_data_alat.kode_alat', $sortDirection );
                    break;
                case 'kategoriSparepart':
                    $query->orderBy ( 'kategori_sparepart.kode', $sortDirection )
                        ->orderBy ( 'kategori_sparepart.nama', $sortDirection );
                    break;
                case 'masterDataSparepart':
                    $query->orderBy ( 'master_data_sparepart.nama', $sortDirection );
                    break;
                case 'partNumber':
                    $query->orderBy ( 'master_data_sparepart.part_number', $sortDirection );
                    break;
                case 'merk':
                    $query->orderBy ( 'master_data_sparepart.merk', $sortDirection );
                    break;
                default:
                    $query->orderBy ( $columnName, $sortDirection );
                    break;
            }
        }
        else
        {
            $query->orderBy ( 'detail_rkb_urgent.updated_at', 'desc' );
        }

        // Pagination
        $draw   = $request->input ( 'draw' );
        $start  = $request->input ( 'start', 0 );
        $length = $request->input ( 'length', 10 );

        $totalRecords = DetailRKBUrgent::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($q) use ($id_rkb)
        {
            $q->where ( 'id_rkb', $id_rkb );
        } )->count ();

        $filteredRecords = $query->count ();

        $data = $query->skip ( $start )->take ( $length )->get ()->map ( function ($item)
        {
            return [ 
                'id'                  => $item->id,
                'namaAlat'            => $item->namaAlat,
                'kodeAlat'            => $item->kodeAlat,
                'kategoriSparepart'   => "{$item->kodeKategoriSparepart}: {$item->namaKategoriSparepart}",
                'masterDataSparepart' => $item->sparepart,
                'partNumber'          => $item->part_number,
                'merk'                => $item->merk,
                'quantity_requested'  => $item->quantity_requested,
                'quantity_approved'   => $item->quantity_approved ?? '-',
                'satuan'              => $item->satuan,
                'aksi'                => '', // Actions rendered on the frontend
            ];
        } );

        return response ()->json ( [ 
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ] );
    }

    public function getDokumentasi ( $id )
    {
        $detailRkbUrgent = DetailRkbUrgent::findOrFail ( $id );

        // Assuming dokumentasi contains the folder path
        $folderPath = $detailRkbUrgent->dokumentasi;

        // Get all files from the folder
        $files = Storage::disk ( 'public' )->files ( $folderPath );

        // Prepare data for response
        $data = array_map ( function ($file)
        {
            return [ 
                'name' => basename ( $file ),
                'url'  => Storage::url ( $file ),
            ];
        }, $files );

        return response ()->json ( [ 'dokumentasi' => $data ] );
    }

}
