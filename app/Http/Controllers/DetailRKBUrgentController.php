<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\LinkRKBDetail;
use App\Models\MasterDataAlat;
use App\Models\DetailRKBUrgent;
use App\Models\KategoriSparepart;
use App\Models\LampiranRKBUrgent;
use App\Models\LinkAlatDetailRKB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DetailRKBUrgentController extends Controller
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

        return view ( 'dashboard.rkb.urgent.detail.detail', [ 
            'headerPage'            => "RKB Urgent",
            'page'                  => 'Detail RKB Urgent [' . $rkb->proyek->nama . ' | ' . $rkb->nomor . ']',
            'proyeks'               => $proyeks,
            'rkb'                   => $rkb,
            'available_alat'        => $available_alat,
            'master_data_sparepart' => MasterDataSparepart::all (),
            'kategori_sparepart'    => KategoriSparepart::all (),
            'TableData'             => $TableData,
            'uniqueValues'          => $uniqueValues,
            'menuContext'           => 'rkb_urgent',
        ] );
    }

    private function getPerPage ( Request $request )
    {
        // If per_page parameter doesn't exist or not -1, redirect with per_page=-1
        if ( ! $request->has ( 'per_page' ) || $request->get ( 'per_page' ) != -1 )
        {
            $parameters             = $request->all ();
            $parameters[ 'per_page' ] = -1;
            $redirectUrl            = $request->url () . '?' . http_build_query ( $parameters );
            return redirect ()->to ( $redirectUrl );
        }

        return false; // Return false instead of number to indicate we want all records
    }

    private function buildQuery ( Request $request, $id )
    {
        $query = DetailRKBUrgent::query ()
            ->select ( [ 
                'detail_rkb_urgent.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_urgent.id', '=', 'link_rkb_detail.id_detail_rkb_urgent' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_urgent.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_urgent.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $id );

        $this->applySearch ( $query, $request );
        $this->applyFilters ( $query, $request );

        return $query;
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
            'nama_koordinator'   => 'detail_rkb_urgent.nama_koordinator', // Updated path
            'satuan'             => 'detail_rkb_urgent.satuan',
            'quantity_requested' => 'detail_rkb_urgent.quantity_requested',
            'quantity_approved'  => 'detail_rkb_urgent.quantity_approved'
        ];

        foreach ( $filterColumns as $paramName => $columnName )
        {
            $this->applyColumnFilter ( $query, $request, $paramName, $columnName );
        }
    }

    private function isNumericColumn ( $columnName )
    {
        return in_array ( $columnName, [ 
            'detail_rkb_urgent.quantity_requested',
            'detail_rkb_urgent.quantity_approved'
        ] );
    }

    private function applyColumnFilter ( $query, Request $request, $paramName, $columnName )
    {
        $selectedParam = "selected_{$paramName}";

        if ( $request->filled ( $selectedParam ) )
        {
            try
            {
                $values = $this->getSelectedValues ( $request->get ( $selectedParam ) );

                if ( $paramName === 'kategori_sparepart' )
                {
                    // Special handling for kategori_sparepart
                    $query->where ( function ($q) use ($values)
                    {
                        foreach ( $values as $value )
                        {
                            if ( $value === 'Empty/Null' )
                            {
                                $q->orWhereNull ( 'kategori_sparepart.nama' )
                                    ->orWhere ( 'kategori_sparepart.nama', '-' )
                                    ->orWhere ( 'kategori_sparepart.nama', '' );
                            }
                            else
                            {
                                // Extract nama from "kode: nama" format
                                $parts = explode ( ': ', $value );
                                if ( count ( $parts ) === 2 )
                                {
                                    $q->orWhere ( function ($subQ) use ($parts)
                                    {
                                        $subQ->where ( 'kategori_sparepart.kode', $parts[ 0 ] )
                                            ->where ( 'kategori_sparepart.nama', $parts[ 1 ] );
                                    } );
                                }
                            }
                        }
                    } );
                }
                else if ( in_array ( $paramName, [ 'quantity_requested', 'quantity_approved' ] ) )
                {
                    // Special handling for numeric columns
                    $query->where ( function ($q) use ($columnName, $values)
                    {
                        $exactValues    = [];
                        $gtValue        = null;
                        $ltValue        = null;
                        $checkboxValues = [];

                        foreach ( $values as $value )
                        {
                            if ( $value === 'null' )
                            {
                                $q->orWhereNull ( $columnName );
                            }
                            elseif ( strpos ( $value, 'exact:' ) === 0 )
                            {
                                $exactValues[] = (int) substr ( $value, 6 );
                            }
                            elseif ( strpos ( $value, 'gt:' ) === 0 )
                            {
                                $gtValue = (int) substr ( $value, 3 );
                            }
                            elseif ( strpos ( $value, 'lt:' ) === 0 )
                            {
                                $ltValue = (int) substr ( $value, 3 );
                            }
                            else
                            {
                                // This is a checkbox value
                                $checkboxValues[] = (int) $value;
                            }
                        }

                        // Handle exact values (including checkbox values)
                        if ( ! empty ( $exactValues ) || ! empty ( $checkboxValues ) )
                        {
                            $allExactValues = array_merge ( $exactValues, $checkboxValues );
                            $q->orWhereIn ( $columnName, $allExactValues );
                        }

                        // Handle range values
                        if ( $gtValue !== null || $ltValue !== null )
                        {
                            $q->orWhere ( function ($rangeQuery) use ($columnName, $gtValue, $ltValue)
                            {
                                if ( $gtValue !== null && $ltValue !== null )
                                {
                                    $rangeQuery->whereBetween ( $columnName, [ $gtValue, $ltValue ] );
                                }
                                elseif ( $gtValue !== null )
                                {
                                    $rangeQuery->where ( $columnName, '>=', $gtValue );
                                }
                                elseif ( $ltValue !== null )
                                {
                                    $rangeQuery->where ( $columnName, '<=', $ltValue );
                                }
                            } );
                        }
                    } );
                }
                else
                {
                    // Original logic for non-numeric columns
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

    private function getUniqueValues ( $query )
    {
        $result = clone $query;
        $data   = $result->get ();

        $formatQuantityValues = function ($column) use ($data)
        {
            return $data->pluck ( $column )
                ->filter ( function ($value)
                {
                    // Only return non-null values since null will be handled by the view's "Empty/Null" option
                    return $value !== null;
                } )
                ->unique ()
                ->map ( function ($value)
                {
                    return (string) $value;
                } )
                ->sort ( function ($a, $b)
                {
                    // Custom sort to ensure numeric order
                    return (int) $a - (int) $b;
                } )
                ->values ();
        };

        return [ 
            'jenis_alat'         => $data->pluck ( 'jenis_alat' )->unique ()->filter ()->sort ()->values (),
            'kode_alat'          => $data->pluck ( 'kode_alat' )->unique ()->filter ()->sort ()->values (),
            'kategori_sparepart' => $data->map ( function ($item)
            {
                return $item->kategori_kode . ': ' . $item->kategori_nama;
            } )->unique ()->filter ()->sort ()->values (),
            'sparepart'          => $data->pluck ( 'sparepart_nama' )->unique ()->filter ()->sort ()->values (),
            'part_number'        => $data->pluck ( 'part_number' )->unique ()->filter ()->sort ()->values (),
            'merk'               => $data->pluck ( 'merk' )->unique ()->filter ()->sort ()->values (),
            'nama_koordinator'   => $data->pluck ( 'nama_koordinator' )->unique ()->filter ()->sort ()->values (), // Add this line
            'satuan'             => $data->pluck ( 'satuan' )->unique ()->filter ()->sort ()->values (),
            'quantity_requested' => $formatQuantityValues ( 'quantity_requested' ),
            'quantity_approved'  => $formatQuantityValues ( 'quantity_approved' ),
        ];
    }

    private function applySearch ( $query, Request $request )
    {
        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'detail_rkb_urgent.satuan', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.jenis_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.kode_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.kode', 'ilike', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.nama', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.nama', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.part_number', 'ilike', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.merk', 'ilike', "%{$search}%" )
                    ->orWhere ( 'detail_rkb_urgent.nama_koordinator', 'ilike', "%{$search}%" ); // Updated path
            } );
        }
    }

    private function getTableData ( $query, $perPage )
    {
        // If $perPage is false (meaning show all), get total count first
        if ( $perPage === false )
        {
            $perPage = $query->count ();
        }

        return $query->orderBy ( 'detail_rkb_urgent.updated_at', 'desc' )
            ->orderBy ( 'detail_rkb_urgent.id', 'desc' )
            ->paginate ( $perPage );
    }

    private function getAlatAvailable ( $rkb )
    {
        return MasterDataAlat::whereHas ( 'alatProyek', function ($query) use ($rkb)
        {
            $query->where ( 'id_proyek', $rkb->id_proyek )
                ->whereNull ( 'removed_at' );
        } )->get ();
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

        // Get dokumentasi files
        $dokumentasi = [];
        if ( $detailRKBUrgent->dokumentasi && Storage::disk ( 'public' )->exists ( $detailRKBUrgent->dokumentasi ) )
        {
            $files       = Storage::disk ( 'public' )->files ( $detailRKBUrgent->dokumentasi );
            $dokumentasi = array_map ( function ($file)
            {
                return [ 
                    'name' => basename ( $file ),
                    'url'  => Storage::url ( $file )
                ];
            }, $files );
        }

        // Format response
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
                'dokumentasi'                     => $dokumentasi,
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
