<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Saldo;
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
use Illuminate\Support\Facades\DB;

class EvaluasiDetailRKBGeneralController extends Controller
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

        $this->rkb = RKB::with ( [ 'proyek' ] )->find ( $id );  // Assign to class property

        // Modified query with proper joins
        $query = $this->buildQuery ( $request, $id );

        // Get ALL stock quantities for the project first, with proper summing
        $allStockQuantities = Saldo::where ( 'id_proyek', $this->rkb->id_proyek )
            ->whereIn ( 'id_master_data_sparepart', function ($query)
            {
                $query->select ( 'id_master_data_sparepart' )
                    ->from ( 'detail_rkb_general' )
                    ->join ( 'link_rkb_detail', 'detail_rkb_general.id', '=', 'link_rkb_detail.id_detail_rkb_general' )
                    ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
                    ->where ( 'link_alat_detail_rkb.id_rkb', $this->rkb->id );
            } )
            ->selectRaw ( 'id_master_data_sparepart, SUM(quantity) as total_quantity' )
            ->groupBy ( 'id_master_data_sparepart' )
            ->get ()
            ->pluck ( 'total_quantity', 'id_master_data_sparepart' );

        // Apply search and non-stock filters first
        $this->applySearch ( $query, $request );
        $this->applyNonStockFilters ( $query, $request );

        // Apply stock quantity filter if selected
        if ( $request->filled ( 'selected_stock_quantity' ) )
        {
            $this->applyStockQuantityFilter ( $query, $request, $allStockQuantities );
        }

        // Get unique values directly from the database, not from filtered data
        $uniqueValues = $this->getUniqueValues ( $id );

        // Get paginated data
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

        // Get stock quantities for each sparepart in this project
        $stockQuantities = Saldo::where ( 'id_proyek', $this->rkb->id_proyek )
            ->get ()
            ->groupBy ( 'id_master_data_sparepart' )
            ->map ( function ($items)
            {
                return $items->sum ( 'quantity' );
            } );

        return view ( 'dashboard.evaluasi.general.detail.detail', [ 
            'headerPage'            => "Evaluasi General",
            'page'                  => 'Detail Evaluasi General [' . $this->rkb->proyek->nama . ' | ' . $this->rkb->nomor . ']',
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

    /**
     * Get unique values directly from the database, not affected by current filters
     */
    private function getUniqueValues ( $rkbId )
    {
        // Get the base query without any filters
        $baseQuery = DetailRKBGeneral::query ()
            ->select ( [ 
                'detail_rkb_general.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk',
                'master_data_sparepart.id as sparepart_id'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_general.id', '=', 'link_rkb_detail.id_detail_rkb_general' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_general.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_general.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->where ( 'link_alat_detail_rkb.id_rkb', $rkbId );

        // Get all data without any filters
        $allData = $baseQuery->get ();

        // Get stock quantities for all spareparts in this project
        $stockQuantities = Saldo::where ( 'id_proyek', $this->rkb->id_proyek )
            ->selectRaw ( 'id_master_data_sparepart, SUM(quantity) as total_quantity' )
            ->groupBy ( 'id_master_data_sparepart' )
            ->get ()
            ->pluck ( 'total_quantity', 'id_master_data_sparepart' );

        // Get stock quantities for all filtered spareparts
        $sparepartIds = $allData->pluck ( 'id_master_data_sparepart' )->unique ();
        $stockValues  = collect ();

        foreach ( $sparepartIds as $sparepartId )
        {
            if ( $stockQuantities->has ( $sparepartId ) )
            {
                $stockValues->push ( $stockQuantities[ $sparepartId ] );
            }
            else
            {
                $stockValues->push ( null ); // No stock = null, not 0
            }
        }

        return [ 
            'jenis_alat'         => $allData->pluck ( 'jenis_alat' )->filter ()->unique ()->sort ()->values (),
            'kode_alat'          => $allData->pluck ( 'kode_alat' )->filter ()->unique ()->sort ()->values (),
            'kategori_sparepart' => $allData->map ( function ($item)
            {
                return $item->kategori_kode . ': ' . $item->kategori_nama;
            } )->filter ()->unique ()->sort ()->values (),
            'sparepart'          => $allData->pluck ( 'sparepart_nama' )->filter ()->unique ()->sort ()->values (),
            'part_number'        => $allData->pluck ( 'part_number' )->filter ()->unique ()->sort ()->values (),
            'merk'               => $allData->pluck ( 'merk' )->filter ()->unique ()->sort ()->values (),
            'quantity_requested' => $allData->pluck ( 'quantity_requested' )->filter ()->unique ()->sort ()->values (),
            'stock_quantity'     => $stockValues->filter ()->unique ()->sort ()->values (),
            'satuan'             => $allData->pluck ( 'satuan' )->filter ()->unique ()->sort ()->values (),
        ];
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
            'quantity_requested' => 'detail_rkb_general.quantity_requested',
            'stock_quantity'     => 'saldo.quantity',
            'satuan'             => 'detail_rkb_general.satuan'
        ];

        // Get stock quantities first
        $stockQuantities = Saldo::where ( 'id_proyek', $this->rkb->id_proyek )
            ->get ()
            ->groupBy ( 'id_master_data_sparepart' )
            ->map ( function ($items)
            {
                return $items->sum ( 'quantity' );
            } );

        foreach ( $filterColumns as $paramName => $columnName )
        {
            // Special handling for stock quantity to include nulls
            if ( $paramName === 'stock_quantity' )
            {
                $this->applyStockQuantityFilter ( $query, $request, $stockQuantities );
            }
            else
            {
                $this->applyColumnFilter ( $query, $request, $paramName, $columnName );
            }
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

                // Special handling for numeric columns
                if ( in_array ( $paramName, [ 'quantity_requested', 'stock_quantity' ] ) )
                {
                    $query->where ( function ($q) use ($columnName, $values)
                    {
                        $hasGtFilter        = false;
                        $hasLtFilter        = false;
                        $gtValue            = null;
                        $ltValue            = null;
                        $hasEmptyNullFilter = false;

                        // First pass to collect range values and check for Empty/Null
                        foreach ( $values as $value )
                        {
                            if ( $value === 'Empty/Null' )
                            {
                                $hasEmptyNullFilter = true;
                            }
                            elseif ( strpos ( $value, 'gt:' ) === 0 )
                            {
                                $hasGtFilter = true;
                                $gtValue     = substr ( $value, 3 );
                            }
                            elseif ( strpos ( $value, 'lt:' ) === 0 )
                            {
                                $hasLtFilter = true;
                                $ltValue     = substr ( $value, 3 );
                            }
                        }

                        // If we have both gt and lt, treat as range
                        if ( $hasGtFilter && $hasLtFilter )
                        {
                            $q->where ( function ($subQ) use ($columnName, $gtValue, $ltValue, $hasEmptyNullFilter)
                            {
                                $subQ->whereBetween ( $columnName, [ $gtValue, $ltValue ] );
                                if ( $hasEmptyNullFilter )
                                {
                                    $subQ->orWhereNull ( $columnName )
                                        ->orWhere ( $columnName, '=', 0 );
                                }
                            } );
                        }
                        else
                        {
                            // Handle individual conditions
                            foreach ( $values as $value )
                            {
                                if ( strpos ( $value, 'exact:' ) === 0 )
                                {
                                    $exactValue = substr ( $value, 6 );
                                    $q->orWhere ( $columnName, '=', $exactValue );
                                }
                                elseif ( strpos ( $value, 'gt:' ) === 0 && ! $hasLtFilter )
                                {
                                    $gtValue = substr ( $value, 3 );
                                    $q->orWhere ( $columnName, '>=', $gtValue );
                                }
                                elseif ( strpos ( $value, 'lt:' ) === 0 && ! $hasGtFilter )
                                {
                                    $ltValue = substr ( $value, 3 );
                                    $q->orWhere ( $columnName, '<=', $ltValue );
                                }
                                elseif ( $value === 'Empty/Null' )
                                {
                                    // Modified this part to handle empty/null values correctly
                                    $q->orWhereNull ( $columnName )
                                        ->orWhere ( $columnName, '=', 0 );
                                }
                                elseif ( ! $hasGtFilter && ! $hasLtFilter && is_numeric ( $value ) )
                                {
                                    $q->orWhere ( $columnName, '=', $value );
                                }
                            }
                        }
                    } );
                }
                // Default handling for non-numeric columns
                else
                {
                    if ( in_array ( 'Empty/Null', $values ) )
                    {
                        $nonNullValues = array_filter ( $values, fn ( $value ) => $value !== 'Empty/Null' );
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

                // Initialize flags for range filtering
                $hasGtFilter        = false;
                $hasLtFilter        = false;
                $gtValue            = null;
                $ltValue            = null;
                $exactValues        = [];
                $hasEmptyNullFilter = false;

                // First pass to categorize values
                foreach ( $values as $value )
                {
                    if ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $hasGtFilter = true;
                        $gtValue     = substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $hasLtFilter = true;
                        $ltValue     = substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValues[] = substr ( $value, 6 );
                    }
                    elseif ( $value === 'Empty/Null' )
                    {
                        $hasEmptyNullFilter = true;
                    }
                    elseif ( is_numeric ( $value ) )
                    {
                        // Direct numeric values from checkboxes
                        $exactValues[] = $value;
                    }
                }

                $query->where ( function ($q) use ($values, $stockQuantities, $hasGtFilter, $hasLtFilter, $gtValue, $ltValue, $exactValues, $hasEmptyNullFilter)
                {
                    // Handle range filter (gt AND lt)
                    if ( $hasGtFilter && $hasLtFilter )
                    {
                        $matchingIds = $stockQuantities->filter ( function ($qty) use ($gtValue, $ltValue)
                        {
                            return $qty >= $gtValue && $qty <= $ltValue;
                        } )->keys ();
                        if ( ! empty ( $matchingIds ) )
                        {
                            $q->whereIn ( 'master_data_sparepart.id', $matchingIds );
                        }
                    }

                    // Handle exact values from both input and checkboxes
                    if ( ! empty ( $exactValues ) )
                    {
                        foreach ( $exactValues as $exactValue )
                        {
                            $matchingIds = $stockQuantities->filter ( function ($qty) use ($exactValue)
                            {
                                return (float) $qty === (float) $exactValue;
                            } )->keys ();
                            if ( ! empty ( $matchingIds ) )
                            {
                                $q->orWhereIn ( 'master_data_sparepart.id', $matchingIds );
                            }
                        }
                    }

                    // Handle individual gt filter
                    if ( $hasGtFilter && ! $hasLtFilter )
                    {
                        $matchingIds = $stockQuantities->filter ( function ($qty) use ($gtValue)
                        {
                            return $qty >= $gtValue;
                        } )->keys ();
                        if ( ! empty ( $matchingIds ) )
                        {
                            $q->orWhereIn ( 'master_data_sparepart.id', $matchingIds );
                        }
                    }

                    // Handle individual lt filter
                    if ( $hasLtFilter && ! $hasGtFilter )
                    {
                        $matchingIds = $stockQuantities->filter ( function ($qty) use ($ltValue)
                        {
                            return $qty <= $ltValue;
                        } )->keys ();
                        if ( ! empty ( $matchingIds ) )
                        {
                            $q->orWhereIn ( 'master_data_sparepart.id', $matchingIds );
                        }
                    }

                    // Handle Empty/Null filter
                    if ( $hasEmptyNullFilter )
                    {
                        $q->orWhere ( function ($subQ)
                        {
                            $subQ->whereDoesntHave ( 'masterDataSparepart.saldos', function ($saldoQuery)
                            {
                                $saldoQuery->where ( 'id_proyek', $this->rkb->id_proyek );
                            } )->orWhereHas ( 'masterDataSparepart.saldos', function ($saldoQuery)
                            {
                                $saldoQuery->where ( 'id_proyek', $this->rkb->id_proyek )
                                    ->where ( 'quantity', 0 );
                            } );
                        } );
                    }
                } );
            }
            catch ( \Exception $e )
            {
                \Log::error ( "Error in stock_quantity filter: " . $e->getMessage () );
            }
        }
    }

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
            DetailRKBGeneral::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
            {
                $query->where ( 'id', $id_rkb );
            } )->update ( [ 'quantity_approved' => null ] );

            $rkb->is_evaluated = false;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_general.detail.index', $id_rkb )
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
            $updated = DetailRKBGeneral::where ( "id", $id )->update ( [ 
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
                ->route ( 'evaluasi_rkb_general.detail.index', $id_rkb )
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
            DetailRKBGeneral::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
            {
                $query->where ( 'id', $id_rkb );
            } )->update ( [ 'quantity_remainder' => 0 ] );

            $rkb->is_approved_svp = false;
            $rkb->svp_approved_at = null;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_general.detail.index', $id_rkb )
                ->with ( 'success', 'Approve RKB oleh SVP berhasil dibatalkan!' );
        }

        // Check if can be approved by SVP
        if ( ! $rkb->is_approved_vp )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB harus di-approve oleh VP terlebih dahulu!' );
        }

        // Update all DetailRKBGeneral records for this RKB
        DetailRKBGeneral::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
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

    private function buildQuery ( $request, $id )
    {
        return DetailRKBGeneral::query ()
            ->select ( [ 
                'detail_rkb_general.*',
                'master_data_alat.jenis_alat',
                'master_data_alat.kode_alat',
                'kategori_sparepart.kode as kategori_kode',
                'kategori_sparepart.nama as kategori_nama',
                'master_data_sparepart.nama as sparepart_nama',
                'master_data_sparepart.part_number',
                'master_data_sparepart.merk',
                'saldo.quantity as stock_quantity',
                'master_data_sparepart.id as sparepart_id'
            ] )
            ->join ( 'link_rkb_detail', 'detail_rkb_general.id', '=', 'link_rkb_detail.id_detail_rkb_general' )
            ->join ( 'link_alat_detail_rkb', 'link_rkb_detail.id_link_alat_detail_rkb', '=', 'link_alat_detail_rkb.id' )
            ->join ( 'master_data_alat', 'link_alat_detail_rkb.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'kategori_sparepart', 'detail_rkb_general.id_kategori_sparepart_sparepart', '=', 'kategori_sparepart.id' )
            ->join ( 'master_data_sparepart', 'detail_rkb_general.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->leftJoin ( 'saldo', function ($join)
            {
                $join->on ( 'master_data_sparepart.id', '=', 'saldo.id_master_data_sparepart' )
                    ->where ( 'saldo.id_proyek', '=', $this->rkb->id_proyek );
            } )
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
            'quantity_requested' => 'detail_rkb_general.quantity_requested',
            'satuan'             => 'detail_rkb_general.satuan'
        ];

        foreach ( $filterColumns as $paramName => $columnName )
        {
            $this->applyColumnFilter ( $query, $request, $paramName, $columnName );
        }
    }
}
