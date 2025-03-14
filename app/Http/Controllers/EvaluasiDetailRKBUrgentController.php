<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Models\DetailRKBUrgent;
use App\Models\KategoriSparepart;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EvaluasiDetailRKBUrgentController extends Controller
{
    private $rkb;  // Add this class property

    public function index ( Request $request, $id )
    {
        if ( $request->get ( 'per_page' ) != -1 )
        {
            $parameters               = $request->except ( 'per_page' );
            $parameters[ 'per_page' ] = -1;

            return redirect ()->to ( $request->url () . '?' . http_build_query ( $parameters ) );
        }

        $perPage = (int) $request->per_page;

        $this->rkb = RKB::with ( [ 'proyek' ] )->find ( $id );

        // Build and filter query
        $query = $this->buildQuery ( $request, $id );

        // Apply search and filters
        $this->applySearch ( $query, $request );
        $this->applyNonStockFilters ( $query, $request );

        // Clone query for stock quantities with current filters
        $currentQuery         = clone $query;
        $filteredSparepartIds = $currentQuery->pluck ( 'master_data_sparepart.id' )->unique ();

        // Get ALL stock quantities for the project, not just filtered ones
        $stockQuantities = Saldo::where ( 'id_proyek', $this->rkb->id_proyek )
            ->whereIn ( 'id_master_data_sparepart', function ($query)
            {
                $query->select ( 'id_master_data_sparepart' )
                    ->from ( 'detail_rkb_urgent' )
                    ->join ( 'link_rkb_detail', 'detail_rkb_urgent.id', '=', 'link_rkb_detail.id_detail_rkb_urgent' )
                    ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
                    ->where ( 'link_alat_detail_rkb.id_rkb', $this->rkb->id );
            } )
            ->selectRaw ( 'id_master_data_sparepart, SUM(quantity) as total_quantity' )
            ->groupBy ( 'id_master_data_sparepart' )
            ->get ()
            ->pluck ( 'total_quantity', 'id_master_data_sparepart' )
            ->filter ( function ($value)
            {
                return $value !== null && $value !== '';
            } );

        // Apply stock quantity filter
        if ( $request->filled ( 'selected_stock_quantity' ) )
        {
            $this->applyStockQuantityFilter ( $query, $request, $stockQuantities );
        }

        // Get filtered data
        $finalFilteredQuery = clone $query;
        $finalFilteredData  = $finalFilteredQuery->get ();

        // Get unique values from filtered data
        $uniqueValues = $this->getUniqueValues ( $finalFilteredQuery );

        // Get stock quantities and table data
        $TableData = $this->getTableData ( $query, $perPage );

        $available_alat = MasterDataAlat::whereHas ( 'alatProyek', function ($query)
        {
            $query->where ( 'id_proyek', $this->rkb->id_proyek )
                ->whereNull ( 'removed_at' );
        } )
            ->where ( 'kode_alat', '!=', 'Workshop' )
            ->get ();

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

        return view ( 'dashboard.evaluasi.urgent.detail.detail', [ 
            'headerPage'            => "Evaluasi Urgent",
            'page'                  => 'Detail Evaluasi Urgent [' . $this->rkb->proyek->nama . ' | ' . $this->rkb->nomor . ']',
            'menuContext'           => 'evaluasi_urgent',

            'proyeks'               => $proyeks,
            'rkb'                   => $this->rkb,
            'available_alat'        => $available_alat,
            'master_data_sparepart' => MasterDataSparepart::all (),
            'kategori_sparepart'    => KategoriSparepart::all (),
            'TableData'             => $TableData,
            'stockQuantities'       => $stockQuantities,
            'uniqueValues'          => $uniqueValues,
        ] );
    }

    private function buildQuery ( $request, $id )
    {
        return DetailRKBUrgent::query ()
            ->select ( [ 
                'detail_rkb_urgent.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk',
                'master_data_sparepart.id as sparepart_id'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_urgent.id', '=', 'link_rkb_detail.id_detail_rkb_urgent' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_urgent.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_urgent.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $id )
            ->orderBy ( 'master_data_sparepart.part_number' );
    }

    private function applyNonStockFilters ( $query, Request $request )
    {
        $filterColumns = [ 
            'jenis_alat'         => 'master_data_alat.jenis_alat',
            'kode_alat'          => 'master_data_alat.kode_alat',
            'kategori_sparepart' => 'kategori_sparepart.nama',
            'sparepart'          => 'master_data_sparepart.nama',
            'part_number'        => 'master_data_sparepart.part_number',
            'merk'               => 'master_data_sparepart.merk',
            'nama_koordinator'   => 'detail_rkb_urgent.nama_koordinator', // Updated path
            'quantity_requested' => 'detail_rkb_urgent.quantity_requested',
            'satuan'             => 'detail_rkb_urgent.satuan',
        ];

        foreach ( $filterColumns as $paramName => $columnName )
        {
            $this->applyColumnFilter ( $query, $request, $paramName, $columnName );
        }
    }

    private function applyColumnFilter ( $query, Request $request, $paramName, $columnName )
    {
        $selectedParam = "selected_{$paramName}";

        if ( $request->filled ( $selectedParam ) )
        {
            try
            {
                $values = $this->getSelectedValues ( $request->get ( $selectedParam ) );

                // Special handling for kategori_sparepart which uses "code: name" format in the UI
                if ( $paramName === 'kategori_sparepart' )
                {
                    $query->where ( function ($q) use ($values)
                    {
                        foreach ( $values as $value )
                        {
                            // Check if it's in the "kode: nama" format
                            if ( strpos ( $value, ': ' ) !== false )
                            {
                                list( $kode, $nama ) = explode ( ': ', $value, 2 );
                                $q->orWhere ( function ($subQ) use ($kode, $nama)
                                {
                                    $subQ->where ( 'kategori_sparepart.kode', $kode )
                                        ->where ( 'kategori_sparepart.nama', $nama );
                                } );
                            }
                            else
                            {
                                // Fallback - search in both columns
                                $q->orWhere ( 'kategori_sparepart.nama', $value )
                                    ->orWhere ( 'kategori_sparepart.kode', $value );
                            }
                        }
                    } );
                    return;
                }

                // Handle numeric columns (quantity_requested and stock_quantity)
                if ( in_array ( $paramName, [ 'quantity_requested', 'stock_quantity' ] ) )
                {
                    $this->applyNumericFilter ( $query, $columnName, $values );
                    return;
                }

                // Original handling for non-numeric columns
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
            catch ( \Exception $e )
            {
                \Log::error ( "Error in {$paramName} filter: " . $e->getMessage () );
            }
        }
    }

    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];

        try
        {
            $decoded = base64_decode ( $paramValue );
            if ( ! $decoded ) return [];

            return explode ( '||', $decoded );
        }
        catch ( \Exception $e )
        {
            \Log::error ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
    }

    private function applyNumericFilter ( $query, $columnName, $values )
    {
        $query->where ( function ($q) use ($columnName, $values)
        {
            $hasCondition = false;
            $exactValues  = [];
            $gtValue      = null;
            $ltValue      = null;

            foreach ( $values as $value )
            {
                if ( $value === 'Empty/Null' )
                {
                    $q->orWhereNull ( $columnName )
                        ->orWhere ( $columnName, 0 );
                    $hasCondition = true;
                }
                elseif ( strpos ( $value, 'exact:' ) === 0 )
                {
                    $exactValue    = (int) substr ( $value, 6 );
                    $exactValues[] = $exactValue;
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
                    // Handle direct numeric values from checkboxes
                    if ( is_numeric ( $value ) )
                    {
                        $exactValues[] = (int) $value;
                        $hasCondition  = true;
                    }
                }
            }

            // Apply exact value filters
            if ( ! empty ( $exactValues ) )
            {
                $q->orWhereIn ( $columnName, $exactValues );
                $hasCondition = true;
            }

            // Handle range filters
            if ( $gtValue !== null || $ltValue !== null )
            {
                $q->orWhere ( function ($rangeQ) use ($columnName, $gtValue, $ltValue)
                {
                    if ( $gtValue !== null && $ltValue !== null )
                    {
                        $rangeQ->whereBetween ( $columnName, [ $gtValue, $ltValue ] );
                    }
                    elseif ( $gtValue !== null )
                    {
                        $rangeQ->where ( $columnName, '>=', $gtValue );
                    }
                    elseif ( $ltValue !== null )
                    {
                        $rangeQ->where ( $columnName, '<=', $ltValue );
                    }
                } );
                $hasCondition = true;
            }

            // If no valid conditions were added, ensure the query returns no results
            if ( ! $hasCondition )
            {
                $q->whereRaw ( '1 = 0' );
            }
        } );
    }

    private function applyStockQuantityFilter ( $query, Request $request, $stockQuantities )
    {
        $selectedParam = "selected_stock_quantity";

        if ( $request->filled ( $selectedParam ) )
        {
            try
            {
                $values = $this->getSelectedValues ( $request->get ( $selectedParam ) );
                if ( empty ( $values ) )
                {
                    return;
                }

                $query->where ( function ($q) use ($values, $stockQuantities)
                {
                    $hasCondition = false;
                    $exactValues  = [];
                    $gtValue      = null;
                    $ltValue      = null;

                    foreach ( $values as $value )
                    {
                        if ( $value === 'Empty/Null' )
                        {
                            $q->orWhere ( function ($subQ)
                            {
                                $subQ->whereDoesntHave ( 'masterDataSparepart.saldos', function ($saldoQ)
                                {
                                    $saldoQ->where ( 'id_proyek', $this->rkb->id_proyek );
                                } )->orWhereHas ( 'masterDataSparepart.saldos', function ($saldoQ)
                                {
                                    $saldoQ->where ( 'id_proyek', $this->rkb->id_proyek )
                                        ->where ( 'quantity', 0 );
                                } );
                            } );
                            $hasCondition = true;
                        }
                        elseif ( strpos ( $value, 'exact:' ) === 0 )
                        {
                            $gtValue       = null;
                            $ltValue       = null;
                            $exactValue    = (int) substr ( $value, 6 );
                            $exactValues[] = $exactValue;
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
                            // Handle direct numeric values from checkboxes
                            $numericValue = (int) $value;
                            if ( $numericValue > 0 || $value === '0' )
                            {
                                $exactValues[] = $numericValue;
                                $hasCondition  = true;
                            }
                        }
                    }

                    // Apply exact value filters
                    if ( ! empty ( $exactValues ) )
                    {
                        $sparepartIds = $stockQuantities->filter ( function ($qty) use ($exactValues)
                        {
                            return in_array ( $qty, $exactValues );
                        } )->keys ();

                        if ( $sparepartIds->isNotEmpty () )
                        {
                            $q->orWhereIn ( 'master_data_sparepart.id', $sparepartIds );
                            $hasCondition = true;
                        }
                    }

                    // Handle range filters
                    if ( $gtValue !== null || $ltValue !== null )
                    {
                        $q->orWhere ( function ($rangeQ) use ($gtValue, $ltValue, $stockQuantities)
                        {
                            $sparepartIds = $stockQuantities->filter ( function ($qty) use ($gtValue, $ltValue)
                            {
                                $meetsMin = $gtValue === null || $qty >= $gtValue;
                                $meetsMax = $ltValue === null || $qty <= $ltValue;
                                return $meetsMin && $meetsMax;
                            } )->keys ();

                            if ( $sparepartIds->isNotEmpty () )
                            {
                                $rangeQ->whereIn ( 'master_data_sparepart.id', $sparepartIds );
                            }
                        } );
                        $hasCondition = true;
                    }

                    // If no valid conditions were added, ensure the query returns no results
                    if ( ! $hasCondition )
                    {
                        $q->where ( 'master_data_sparepart.id', '=', null );
                    }
                } );
            }
            catch ( \Exception $e )
            {
                \Log::error ( "Error in stock_quantity filter: " . $e->getMessage () );
            }
        }
    }

    private function getStockQuantities ( $projectId )
    {
        return Saldo::where ( 'id_proyek', $projectId )
            ->get ()
            ->groupBy ( 'id_master_data_sparepart' )
            ->map ( function ($items)
            {
                return $items->sum ( 'quantity' );
            } );
    }

    private function getTableData ( $query, $perPage )
    {
        if ( $perPage === -1 )
        {
            $data = $query->get ();
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $data,
                $data->count (),
                max ( $data->count (), 1 ),
                1
            );
        }

        return $query->paginate ( $perPage );
    }

    private function applySearch ( $query, Request $request )
    {
        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'detail_rkb_urgent.satuan', 'ilike', "%{$search}%" )
                    ->orWhere ( 'detail_rkb_urgent.nama_koordinator', 'ilike', "%{$search}%" ) // Updated path
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

    private function getUniqueValues ( $query )
    {
        // Instead of using filtered query, get all DetailRKBUrgent records for the RKB
        $allRecords = DetailRKBUrgent::query ()
            ->select ( [ 
                'detail_rkb_urgent.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk',
                'master_data_sparepart.id as sparepart_id'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_urgent.id', '=', 'link_rkb_detail.id_detail_rkb_urgent' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_urgent.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_urgent.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $this->rkb->id )
            ->get ();

        // Get all sparepart IDs from the RKB
        $allSparepartIds = $allRecords->pluck ( 'id_master_data_sparepart' )->unique ();

        // Get stock quantities directly from the database for all spareparts in the RKB
        $stockQuantities = Saldo::where ( 'id_proyek', $this->rkb->id_proyek )
            ->whereIn ( 'id_master_data_sparepart', $allSparepartIds )
            ->selectRaw ( 'id_master_data_sparepart, SUM(quantity) as total_quantity' )
            ->groupBy ( 'id_master_data_sparepart' )
            ->get ()
            ->pluck ( 'total_quantity' )
            ->reject ( function ($value)
            {
                return $value === null;
            } )
            ->unique ()
            ->map ( function ($value)
            {
                return (string) $value;
            } )
            ->sort ()
            ->values ();

        // Format quantity values consistently
        $formatQuantityValues = function ($column) use ($allRecords)
        {
            return $allRecords->pluck ( $column )
                ->reject ( function ($value)
                {
                    return $value === null;
                } )
                ->unique ()
                ->map ( function ($value)
                {
                    return (string) $value;
                } )
                ->sort ()
                ->values ();
        };

        return [ 
            'jenis_alat'         => $allRecords->pluck ( 'jenis_alat' )->unique ()->filter ()->sort ()->values (),
            'kode_alat'          => $allRecords->pluck ( 'kode_alat' )->unique ()->filter ()->sort ()->values (),
            'kategori_sparepart' => $allRecords->map ( function ($item)
            {
                return $item->kategori_kode . ': ' . $item->kategori_nama;
            } )->unique ()->filter ()->sort ()->values (),
            'sparepart'          => $allRecords->pluck ( 'sparepart_nama' )->unique ()->filter ()->sort ()->values (),
            'part_number'        => $allRecords->pluck ( 'part_number' )->unique ()->filter ()->sort ()->values (),
            'merk'               => $allRecords->pluck ( 'merk' )->unique ()->filter ()->sort ()->values (),
            'nama_koordinator'   => $allRecords->pluck ( 'nama_koordinator' )->unique ()->filter ()->sort ()->values (),
            'quantity_requested' => $formatQuantityValues ( 'quantity_requested' ),
            'stock_quantity'     => $stockQuantities,
            'satuan'             => $allRecords->pluck ( 'satuan' )->unique ()->filter ()->sort ()->values (),
        ];
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


    public function evaluate ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // If already evaluated, cancel evaluation
        if ( $rkb->is_evaluated )
        {
            // Cannot cancel if already approved
            if ( $rkb->is_approved )
            {
                return redirect ()
                    ->back ()
                    ->with ( 'error', 'Tidak dapat membatalkan evaluasi RKB yang sudah di-approve!' );
            }

            // Reset all quantity_approved values to 0
            DetailRKBUrgent::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
            {
                $query->where ( 'id', $id_rkb );
            } )->update ( [ 'quantity_approved' => null ] );

            $rkb->is_evaluated = false;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
                ->with ( 'success', 'Evaluasi RKB berhasil dibatalkan!' );
        }

        // Existing evaluation logic
        $request->validate ( [ 
            "quantity_approved"   => "required|array",
            "quantity_approved.*" => "required|integer|min:0",
        ] );

        // Ambil data dari input
        $quantityApproved = $request->input ( "quantity_approved" );

        // Loop untuk mengupdate setiap baris berdasarkan ID
        foreach ( $quantityApproved as $id => $quantity )
        {
            $updated = DetailRKBUrgent::where ( "id", $id )->update ( [ 
                "quantity_approved" => $quantity,
            ] );

            // Debug jika update gagal
            if ( ! $updated )
            {
                return redirect ()
                    ->back ()
                    ->with ( "error", "Gagal mengupdate data untuk ID {$id}" );
            }
        }

        $rkb               = RKB::find ( $id_rkb );
        $rkb->is_evaluated = true;
        $rkb->save ();

        // Redirect dengan pesan sukses
        return redirect ()
            ->back ()
            ->with ( "success", "RKB Berhasil di Evaluasi!" );
    }

    public function approveVP ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // If already approved by VP, cancel approval
        if ( $rkb->is_approved_vp )
        {
            $rkb->is_approved_vp = false;
            $rkb->vp_approved_at = null;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
                ->with ( 'success', 'Approve RKB oleh VP berhasil dibatalkan!' );
        }

        // Check if can be approved by VP
        if ( ! $rkb->is_evaluated )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB harus dievaluasi terlebih dahulu!' );
        }

        $rkb->is_approved_vp = true;
        $rkb->vp_approved_at = now ();
        $rkb->save ();

        return redirect ()
            ->back ()
            ->with ( 'success', 'RKB Berhasil di Approve oleh VP!' );
    }

    public function approveSVP ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // If already approved by SVP, cancel approval
        if ( $rkb->is_approved_svp )
        {
            // Reset quantity_remainder values to 0
            DetailRKBUrgent::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
            {
                $query->where ( 'id', $id_rkb );
            } )->update ( [ 'quantity_remainder' => 0 ] );

            $rkb->is_approved_svp = false;
            $rkb->svp_approved_at = null;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
                ->with ( 'success', 'Approve RKB oleh SVP berhasil dibatalkan!' );
        }

        // Check if can be approved by SVP
        if ( ! $rkb->is_approved_vp )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB harus di-approve oleh VP terlebih dahulu!' );
        }

        // Update all DetailRKBUrgent records for this RKB
        DetailRKBUrgent::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
        {
            $query->where ( 'id', $id_rkb );
        } )->each ( function ($detail)
        {
            $detail->incrementQuantityRemainder ( $detail->quantity_approved );
        } );

        $rkb->is_approved_svp = true;
        $rkb->svp_approved_at = now ();
        $rkb->save ();

        return redirect ()
            ->back ()
            ->with ( 'success', 'RKB Berhasil di Approve oleh SVP!' );
    }

    public function getKronologi ( $id )
    {
        $detailRkbUrgent = DetailRkbUrgent::findOrFail ( $id );
        return response ()->json ( [ 'kronologi' => $detailRkbUrgent->kronologi ] );
    }
}
