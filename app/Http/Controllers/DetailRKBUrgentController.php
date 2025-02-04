<?php

namespace App\Http\Controllers;

use App\Models\LampiranRKBUrgent;
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
    public function index ( Request $request, $id )
    {
        $allowedPerPage = [ 10, 25, 50, 100, -1 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $rkb = RKB::with ( [ 'proyek' ] )->find ( $id );

        // Modified query with proper joins
        $query = DetailRKBUrgent::query ()
            ->join ( 'link_rkb_detail', 'detail_rkb_urgent.id', '=', 'link_rkb_detail.id_detail_rkb_urgent' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_urgent.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_urgent.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $id )
            ->select ( [ 
                'detail_rkb_urgent.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode',
                'kategori_sparepart.nama',
                'master_data_sparepart.nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk'
            ] );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'detail_rkb_urgent.satuan', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.jenis_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.kode_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.kode', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.part_number', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.merk', 'like', "%{$search}%" )
                    ->orWhere ( 'link_alat_detail_rkb.nama_koordinator', 'like', "%{$search}%" ); // Added this line
            } );
        }

        $available_alat = MasterDataAlat::whereHas ( 'alatProyek', function ($query) use ($rkb)
        {
            $query->where ( 'id_proyek', $rkb->id_proyek )
                ->whereNull ( 'removed_at' );
        } )->get ();

        // Modified TableData to include ordering by master_data_alat.id
        $TableData = $perPage === -1
            ? $query->orderBy ( 'master_data_alat.id', 'asc' )
                ->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $query->count () )
            : $query->orderBy ( 'master_data_alat.id', 'asc' )
                ->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $perPage );

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        return view ( 'dashboard.rkb.urgent.detail.detail', [ 
            'headerPage'            => "RKB Urgent",
            'page'                  => 'Detail RKB Urgent [' . $rkb->proyek->nama . ' | ' . $rkb->nomor . ']',

            'proyeks'               => $proyeks,
            'rkb'                   => $rkb,
            'available_alat'        => $available_alat,
            'master_data_sparepart' => MasterDataSparepart::all (),
            'kategori_sparepart'    => KategoriSparepart::all (),
            'TableData'             => $TableData,

            'menuContext'           => 'rkb_urgent',  // Ensure this flag is passed for detail pages
        ] );

    }

    // Store a new DetailRKBUrgent
    public function store ( Request $request )
    {
        $validatedData = $request->validate ( [ 
            'quantity_requested'              => 'required|integer|min:1',
            'satuan'                          => 'required|string|max:50',
            'nama_koordinator'                => 'required|string|max:50',
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
                'nama_koordinator'                => $validatedData[ 'nama_koordinator' ],
                'kronologi'                       => $validatedData[ 'kronologi' ],
                'dokumentasi'                     => null, // Akan diisi nanti
                'id_kategori_sparepart_sparepart' => $validatedData[ 'id_kategori_sparepart_sparepart' ],
                'id_master_data_sparepart'        => $validatedData[ 'id_master_data_sparepart' ],
            ] );

            // Tentukan folder berdasarkan nomor RKB dan ID DetailRKBUrgent
            $folderPath = "uploads/rkb_urgent/{$rkbNumber}/dokumentasi/{$detailRKBUrgent->id}/";

            // Tangani unggahan file dokumentasi
            if ( $request->hasFile ( 'dokumentasi' ) )
            {
                foreach ( $request->file ( 'dokumentasi' ) as $file )
                {
                    // Format nama file
                    $originalName = pathinfo ( $file->getClientOriginalName (), PATHINFO_FILENAME );
                    $extension    = $file->getClientOriginalExtension ();
                    $timestamp    = now ()->format ( 'Y-m-d--H-i-s' );
                    $fileName     = "{$originalName}___{$timestamp}.{$extension}";

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
                    'nama_koordinator' => $validatedData[ 'nama_koordinator' ], // Atur nilai default
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
                'nama_koordinator'                => $detailRKBUrgent->nama_koordinator,  // Add this line
                'kronologi'                       => $detailRKBUrgent->kronologi,  // Add this line
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
            'nama_koordinator'                => 'required|string|max:50',
            'kronologi'                       => 'required|string|max:1000',
            'id_master_data_alat'             => 'required|integer|exists:master_data_alat,id',
            'id_kategori_sparepart_sparepart' => 'required|integer|exists:kategori_sparepart,id',
            'id_master_data_sparepart'        => 'required|integer|exists:master_data_sparepart,id',
            'id_rkb'                          => 'required|integer|exists:rkb,id',
        ] );

        try
        {
            $updateData = [ 
                'quantity_requested'              => $validatedData[ 'quantity_requested' ],
                'satuan'                          => $validatedData[ 'satuan' ],
                'nama_koordinator'                => $validatedData[ 'nama_koordinator' ],
                'kronologi'                       => $validatedData[ 'kronologi' ],
                'id_kategori_sparepart_sparepart' => $validatedData[ 'id_kategori_sparepart_sparepart' ],
                'id_master_data_sparepart'        => $validatedData[ 'id_master_data_sparepart' ],
            ];

            // Handle dokumentasi update only if files are uploaded
            if ( $request->hasFile ( 'dokumentasi' ) )
            {
                $request->validate ( [ 
                    'dokumentasi.*' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
                ] );

                // Delete old dokumentasi if exists
                if ( $detailRKBUrgent->dokumentasi )
                {
                    Storage::disk ( 'public' )->deleteDirectory ( $detailRKBUrgent->dokumentasi );
                }

                // Get RKB number
                $rkb       = RKB::findOrFail ( $validatedData[ 'id_rkb' ] );
                $rkbNumber = $rkb->nomor;

                // Create new folder path
                $folderPath = "uploads/rkb_urgent/{$rkbNumber}/dokumentasi/{$detailRKBUrgent->id}/";

                // Store new files
                foreach ( $request->file ( 'dokumentasi' ) as $file )
                {
                    $originalName = pathinfo ( $file->getClientOriginalName (), PATHINFO_FILENAME );
                    $extension    = $file->getClientOriginalExtension ();
                    $timestamp    = now ()->format ( 'Y-m-d--H-i-s' );
                    $fileName     = "{$originalName}___{$timestamp}.{$extension}";

                    $file->storeAs ( $folderPath, $fileName, 'public' );
                }

                $updateData[ 'dokumentasi' ] = $folderPath;
            }

            // Update DetailRKBUrgent
            $detailRKBUrgent->update ( $updateData );

            // Update link alat
            // ...existing link update code...

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
        $detailRKBUrgent = DetailRKBUrgent::with ( 'linkRkbDetails.linkAlatDetailRkb.rkb' )->find ( $id );

        if ( ! $detailRKBUrgent )
        {
            return redirect ()->back ()->with ( 'error', 'Detail RKB Urgent not found!' );
        }

        $rkb               = $detailRKBUrgent->linkRkbDetails[ 0 ]->linkAlatDetailRKB->rkb;
        $linkAlatDetailRKB = $detailRKBUrgent->linkRkbDetails[ 0 ]->linkAlatDetailRKB;

        // Delete this DetailRKBUrgent's dokumentasi folder
        if ( $detailRKBUrgent->dokumentasi && Storage::disk ( 'public' )->exists ( $detailRKBUrgent->dokumentasi ) )
        {
            Storage::disk ( 'public' )->deleteDirectory ( $detailRKBUrgent->dokumentasi );
        }

        // Check if this is the last DetailRKBUrgent for this LinkAlatDetailRKB
        $otherDetailRKBUrgents = LinkRKBDetail::where ( 'id_link_alat_detail_rkb', $linkAlatDetailRKB->id )
            ->where ( 'id_detail_rkb_urgent', '!=', $id )
            ->exists ();

        // Always delete LampiranRKBUrgent if it exists
        if ( $linkAlatDetailRKB && $linkAlatDetailRKB->id_lampiran_rkb_urgent )
        {
            LampiranRKBUrgent::where ( 'id', $linkAlatDetailRKB->id_lampiran_rkb_urgent )->delete ();
            $linkAlatDetailRKB->update ( [ 'id_lampiran_rkb_urgent' => null ] );
        }

        // Delete LinkRKBDetail
        foreach ( $detailRKBUrgent->linkRkbDetails as $linkRkbDetail )
        {
            $linkRkbDetail->delete ();
        }

        // If this is the last DetailRKBUrgent for this LinkAlatDetailRKB
        if ( ! $otherDetailRKBUrgents )
        {
            // Delete the entire uploads/rkb_urgent folder
            if ( $rkb )
            {
                $folderPath = 'uploads/rkb_urgent/' . $rkb->nomor;
                if ( Storage::disk ( 'public' )->exists ( $folderPath ) )
                {
                    Storage::disk ( 'public' )->deleteDirectory ( $folderPath );
                }
            }

            // Delete the LinkAlatDetailRKB
            $linkAlatDetailRKB->delete ();
        }

        // Delete DetailRKBUrgent
        $detailRKBUrgent->delete ();

        return redirect ()->back ()->with ( 'success', 'Detail RKB Urgent and its links deleted successfully!' );
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
