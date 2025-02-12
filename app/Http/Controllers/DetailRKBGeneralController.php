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
        $perPage = $this->getPerPage ( $request );
        if ( $perPage instanceof \Illuminate\Http\RedirectResponse )
        {
            return $perPage;
        }

        $rkb            = RKB::with ( [ 'proyek' ] )->find ( $id );
        $query          = $this->buildQuery ( $request, $id );
        $uniqueValues   = $this->getUniqueValues ( $query );
        $TableData      = $this->getTableData ( $query, $perPage );
        $available_alat = $this->getAlatAvailable ( $rkb );
        $proyeks        = $this->getProyeks ();

        return view ( 'dashboard.rkb.general.detail.detail', [ 
            'headerPage'            => "RKB General",
            'page'                  => 'Detail RKB General [' . $rkb->proyek->nama . ' | ' . $rkb->nomor . ']',
            'proyeks'               => $proyeks,
            'rkb'                   => $rkb,
            'available_alat'        => $available_alat,
            'master_data_sparepart' => MasterDataSparepart::all (),
            'kategori_sparepart'    => KategoriSparepart::all (),
            'TableData'             => $TableData,
            'uniqueValues'          => $uniqueValues,
        ] );
    }

    private function getPerPage ( Request $request )
    {
        // If per_page parameter doesn't exist or not -1, redirect with per_page=-1
        if ( ! $request->has ( 'per_page' ) || $request->get ( 'per_page' ) != -1 )
        {
            $parameters               = $request->all ();
            $parameters[ 'per_page' ] = -1;
            $redirectUrl              = $request->url () . '?' . http_build_query ( $parameters );
            return redirect ()->to ( $redirectUrl );
        }

        return false; // Return false instead of number to indicate we want all records
    }

    private function buildQuery ( Request $request, $id )
    {
        $query = DetailRKBGeneral::query ()
            ->select ( [ 
                'detail_rkb_general.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_general.id', '=', 'link_rkb_detail.id_detail_rkb_general' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_general.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_general.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $id );

        $this->applySearch ( $query, $request );
        $this->applyFilters ( $query, $request );

        return $query;
    }

    private function applySearch ( $query, Request $request )
    {
        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'detail_rkb_general.satuan', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.jenis_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.kode_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.kode', 'ilike', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.nama', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.nama', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.part_number', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.merk', 'ilike', "%{$search}%" );
            } );
        }
    }

    private function applyFilters ( $query, Request $request )
    {
        $filterColumns = [ 
            'jenis_alat'         => 'master_data_alat.jenis_alat',
            'kode_alat'          => 'master_data_alat.kode_alat',
            'kategori_sparepart' => 'kategori_sparepart.nama',
            'sparepart'          => 'master_data_sparepart.nama',
            'part_number'        => 'master_data_sparepart.part_number',
            'merk'               => 'master_data_sparepart.merk',
            'satuan'             => 'detail_rkb_general.satuan',
            'quantity_requested' => 'detail_rkb_general.quantity_requested',
            'quantity_approved'  => 'detail_rkb_general.quantity_approved'
        ];

        foreach ( $filterColumns as $paramName => $columnName )
        {
            $this->applyColumnFilter ( $query, $request, $paramName, $columnName );
        }
    }

    private function getTableData ( $query, $perPage )
    {
        // If $perPage is false (meaning show all), get total count first
        if ( $perPage === false )
        {
            $perPage = $query->count ();
        }

        return $query->orderBy ( 'detail_rkb_general.updated_at', 'desc' )
            ->orderBy ( 'detail_rkb_general.id', 'desc' )
            ->paginate ( $perPage );
    }

    private function getProyeks ()
    {
        $user         = Auth::user ();
        $proyeksQuery = Proyek::with ( "users" );

        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        return $proyeksQuery->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();
    }

    private function getAlatAvailable ( $rkb )
    {
        return MasterDataAlat::whereHas ( 'alatProyek', function ($query) use ($rkb)
        {
            $query->where ( 'id_proyek', $rkb->id_proyek )
                ->whereNull ( 'removed_at' );
        } )->get ();
    }

    private function getUniqueValues ( $query )
    {
        $result = clone $query;
        $data   = $result->get ();

        return [ 
            'jenis_alat'         => $data->pluck ( 'jenis_alat' )->unique ()->filter ()->sort ()->values (),
            'kode_alat'          => $data->pluck ( 'kode_alat' )->unique ()->filter ()->sort ()->values (),
            'kategori_sparepart' => $data->pluck ( 'kategori_nama' )->unique ()->filter ()->sort ()->values (),
            'sparepart'          => $data->pluck ( 'sparepart_nama' )->unique ()->filter ()->sort ()->values (),
            'part_number'        => $data->pluck ( 'part_number' )->unique ()->filter ()->sort ()->values (),
            'merk'               => $data->pluck ( 'merk' )->unique ()->filter ()->sort ()->values (),
            'satuan'             => $data->pluck ( 'satuan' )->unique ()->filter ()->sort ()->values (),
            'quantity_requested' => $data->pluck ( 'quantity_requested' )->unique ()->filter ()->sort ()->values (),
            'quantity_approved'  => $data->pluck ( 'quantity_approved' )->unique ()->filter ()->sort ()->values (),
        ];
    }

    private function applyColumnFilter ( $query, Request $request, $paramName, $columnName )
    {
        $selectedParam = "selected_{$paramName}";

        if ( $request->filled ( $selectedParam ) )
        {
            try
            {
                $values = $this->getSelectedValues ( $request->get ( $selectedParam ) );

                // Special handling for numeric columns
                if ( in_array ( $paramName, [ 'quantity_requested', 'quantity_approved' ] ) )
                {
                    if ( in_array ( 'null', $values ) )
                    {
                        $nonNullValues = array_filter ( $values, fn ( $value ) => $value !== 'null' );
                        $query->where ( function ($q) use ($columnName, $nonNullValues)
                        {
                            $q->whereNull ( $columnName )
                                ->when ( ! empty ( $nonNullValues ), function ($subQ) use ($columnName, $nonNullValues)
                                {
                                    $subQ->orWhereIn ( $columnName, $nonNullValues );
                                } );
                        } );
                    }
                    else
                    {
                        $query->whereIn ( $columnName, $values );
                    }
                }
                // Regular handling for text columns
                else
                {
                    if ( in_array ( 'null', $values ) )
                    {
                        $nonNullValues = array_filter ( $values, fn ( $value ) => $value !== 'null' );
                        $query->where ( function ($q) use ($columnName, $nonNullValues)
                        {
                            $q->whereNull ( $columnName )
                                ->orWhere ( $columnName, '-' )
                                ->orWhere ( $columnName, '' )
                                ->when ( ! empty ( $nonNullValues ), function ($subQ) use ($columnName, $nonNullValues)
                                {
                                    $subQ->orWhereIn ( $columnName, $nonNullValues );
                                } );
                        } );
                    }
                    else
                    {
                        $query->whereIn ( $columnName, $values );
                    }
                }
            }
            catch ( \Exception $e )
            {
                \Log::error ( "Error in {$paramName} filter: " . $e->getMessage () );
            }
        }
    }

    // Add this new helper method
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];

        try
        {
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            \Log::error ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
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
