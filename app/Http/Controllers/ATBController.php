<?php
namespace App\Http\Controllers;

use App\Models\ATB;
use App\Models\RKB;
use App\Models\SPB;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\DetailSPB;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SaldoController;
use App\Models\KategoriSparepart; // Add this line

class ATBController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showAtbPage (
            "Hutang Unit Alat",
            "Data ATB Hutang Unit Alat",
            $request->id_proyek
        );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showAtbPage (
            "Panjar Unit Alat",
            "Data ATB Panjar Unit Alat",
            $request->id_proyek
        );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showAtbPage (
            "Mutasi Proyek",
            "Data ATB Mutasi Proyek",
            $request->id_proyek
        );
    }

    public function panjar_proyek ( Request $request )
    {
        return $this->showAtbPage (
            "Panjar Proyek",
            "Data ATB Panjar Proyek",
            $request->id_proyek
        );
    }

    /**
     * Decodes base64 encoded selected values into an array
     * 
     * @param string $paramValue Base64 encoded selected values
     * @return array Array of selected values
     */
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue )
        {
            return [];
        }

        try
        {
            // Values are separated by || instead of commas for better compatibility
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            \Log::error ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
    }

    private function showAtbPage ( $tipe, $pageTitle, $id_proyek )
    {
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) request ()->get ( 'per_page' ), $allowedPerPage ) ? (int) request ()->get ( 'per_page' ) : 10;

        // Clean and format tipe
        $tipe = strtolower ( str_replace ( ' ', '-', $tipe ) );

        // Get search query
        $search = request ()->get ( 'search', '' );

        // Get base ATB query with relationships
        $query = ATB::with ( [ 
            'spb',
            'masterDataSparepart.kategoriSparepart',
            'masterDataSupplier',
            'detailSpb',
            'apbMutasi',
            'asalProyek'
        ] )
            ->where ( 'id_proyek', $id_proyek )
            ->where ( 'tipe', $tipe );

        // Enhanced search functionality
        if ( $search )
        {
            $query->where ( function ($q) use ($search)
            {
                $searchLower = strtolower ( trim ( $search ) );
                $searchParts = explode ( ' ', $searchLower );

                // Array of Indonesian day names with their database equivalents
                $hariIndonesia = [ 
                    'senin'  => 'Monday',
                    'selasa' => 'Tuesday',
                    'rabu'   => 'Wednesday',
                    'kamis'  => 'Thursday',
                    'jumat'  => 'Friday',
                    "jum'at" => 'Friday',
                    'sabtu'  => 'Saturday',
                    'minggu' => 'Sunday',
                ];

                // Array of Indonesian month names with their numbers
                $bulanIndonesia = [ 
                    'januari'   => '01',
                    'februari'  => '02',
                    'maret'     => '03',
                    'april'     => '04',
                    'mei'       => '05',
                    'juni'      => '06',
                    'juli'      => '07',
                    'agustus'   => '08',
                    'september' => '09',
                    'oktober'   => '10',
                    'november'  => '11',
                    'desember'  => '12',
                ];

                $isDateSearch = false;
                $year         = null;
                $month        = null;
                $day          = null;

                // Check each part of the search string
                foreach ( $searchParts as $part )
                {
                    // Check for year
                    if ( is_numeric ( $part ) && strlen ( $part ) === 4 )
                    {
                        $year         = $part;
                        $isDateSearch = true;
                        continue;
                    }

                    // Check for day name
                    foreach ( $hariIndonesia as $indo => $eng )
                    {
                        if ( str_starts_with ( $indo, $part ) )
                        {
                            $isDateSearch = true;
                            $q->orWhereRaw ( "TO_CHAR(tanggal, 'Day') ILIKE ?", [ $eng . '%' ] );
                            break 2;
                        }
                    }

                    // Check for month name
                    foreach ( $bulanIndonesia as $indo => $num )
                    {
                        if ( str_starts_with ( $indo, $part ) )
                        {
                            $month        = $num;
                            $isDateSearch = true;
                            break;
                        }
                    }

                    // Check for day number
                    if ( is_numeric ( $part ) && strlen ( $part ) <= 2 )
                    {
                        $day          = sprintf ( "%02d", $part );
                        $isDateSearch = true;
                    }
                }

                // Apply date filters based on found components
                if ( $isDateSearch )
                {
                    if ( $year )
                    {
                        $q->whereYear ( 'tanggal', $year );
                    }
                    if ( $month )
                    {
                        $q->whereMonth ( 'tanggal', $month );
                    }
                    if ( $day )
                    {
                        $q->whereDay ( 'tanggal', $day );
                    }
                }
                else
                {
                    // Existing non-date search criteria
                    $q->where ( function ($q) use ($search)
                    {
                        $q->whereHas ( 'spb', function ($q) use ($search)
                        {
                            $q->where ( 'nomor', 'ilike', "%{$search}%" );
                        } )
                            ->orWhereHas ( 'masterDataSparepart', function ($q) use ($search)
                            {
                                $q->where ( 'nama', 'ilike', "%{$search}%" )
                                    ->orWhere ( 'part_number', 'ilike', "%{$search}%" )
                                    ->orWhere ( 'merk', 'ilike', "%{$search}%" )
                                    ->orWhereHas ( 'kategoriSparepart', function ($q) use ($search)
                                    {
                                        $q->where ( 'kode', 'ilike', "%{$search}%" )
                                            ->orWhere ( 'nama', 'ilike', "%{$search}%" );
                                    } );
                            } )
                            ->orWhereHas ( 'masterDataSupplier', function ($q) use ($search)
                            {
                                $q->where ( 'nama', 'ilike', "%{$search}%" );
                            } )
                            ->orWhereHas ( 'detailSpb', function ($q) use ($search)
                            {
                                $q->where ( 'satuan', 'ilike', "%{$search}%" );
                            } )
                            ->orWhereHas ( 'asalProyek', function ($q) use ($search)
                            {
                                $q->where ( 'nama', 'ilike', "%{$search}%" );
                            } );

                        // For numeric searches
                        if ( is_numeric ( str_replace ( [ ',', '.' ], '', $search ) ) )
                        {
                            $numericSearch = (float) str_replace ( [ ',', '.' ], '', $search );
                            $tolerance     = 0.1; // 10% tolerance
                            $min           = $numericSearch * ( 1 - $tolerance );
                            $max           = $numericSearch * ( 1 + $tolerance );

                            $q->orWhere ( function ($query) use ($numericSearch, $min, $max)
                            {
                                // First, try exact matches
                                $query->where ( function ($q) use ($numericSearch)
                                {
                                    $q->where ( 'atb.quantity', '=', $numericSearch )
                                        ->orWhere ( 'atb.harga', '=', $numericSearch )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga) AS DECIMAL(15,2)) = ?', [ $numericSearch ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga * 0.11) AS DECIMAL(15,2)) = ?', [ $numericSearch ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga * 1.11) AS DECIMAL(15,2)) = ?', [ $numericSearch ] );
                                } );

                                // Then try range matches
                                $query->orWhere ( function ($q) use ($min, $max)
                                {
                                    $q->whereBetween ( 'atb.quantity', [ $min, $max ] )
                                        ->orWhereBetween ( 'atb.harga', [ $min, $max ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga) AS DECIMAL(15,2)) BETWEEN ? AND ?', [ $min, $max ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga * 0.11) AS DECIMAL(15,2)) BETWEEN ? AND ?', [ $min, $max ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga * 1.11) AS DECIMAL(15,2)) BETWEEN ? AND ?', [ $min, $max ] );
                                } );
                            } );
                        }
                    } );
                }
            } );
        }

        // Apply filters - This is where the error happens, so we need to fix the filter application
        $this->applyFilters ( $query, request () );

        // Calculate total amounts for all records before pagination
        $totalQuery = clone $query;
        $totals     = $totalQuery->selectRaw ( '
            SUM(quantity * harga) as total_harga,
            SUM(quantity * harga * 0.11) as total_ppn,
            SUM(quantity * harga * 1.11) as total_bruto
        ' )->first ();

        // Get paginated results
        $TableData = $query->orderBy ( 'tanggal', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Add totals to pagination object
        $TableData->total_harga = $totals->total_harga;
        $TableData->total_ppn   = $totals->total_ppn;
        $TableData->total_bruto = $totals->total_bruto;

        // Get required data
        $proyek = Proyek::with ( "users" )->findOrFail ( $id_proyek );

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

        // Get SPBs if needed
        $spbs = collect ();
        if ( $tipe === 'hutang-unit-alat' )
        {
            $rkbs = RKB::with ( "spbs.linkSpbDetailSpb.detailSpb" )
                ->where ( 'id_proyek', $id_proyek )
                ->get ();

            foreach ( $rkbs as $rkb )
            {
                $filteredSpbs = $rkb->spbs->filter ( function ($spb)
                {
                    $hasRemainingQuantity = $spb->linkSpbDetailSpb->some ( function ($link)
                    {
                        return $link->detailSpb->quantity_belum_diterima > 0;
                    } );

                    return $hasRemainingQuantity &&
                        ( ( ! $spb->is_addendum && ! isset ( $spb->id_spb_original ) ) ||
                            ( $spb->is_addendum && isset ( $spb->id_spb_original ) ) );
                } );

                $spbs = $spbs->merge ( $filteredSpbs );
            }
        }

        // Get additional data based on type
        $spareparts = null;
        if ( $tipe === 'panjar-unit-alat' || $tipe === 'panjar-proyek' )
        {
            $spareparts = MasterDataSparepart::with ( 'KategoriSparepart' )
                ->orderByDesc ( 'updated_at' )
                ->get ();
        }
        elseif ( $tipe === 'hutang-unit-alat' && in_array ( Auth::user ()->role, env ( 'IS_BETA', false ) ? [ 'admin_divisi', 'vp', 'svp', 'superadmin', 'koordinator_proyek' ] : [ 'admin_divisi', 'vp', 'svp', 'superadmin' ] ) )
        {
            $spareparts = MasterDataSparepart::with ( 'KategoriSparepart' )
                ->orderByDesc ( 'updated_at' )
                ->get ();
        }

        // Get common data
        $kategoriSpareparts  = KategoriSparepart::all ();
        $masterDataSuppliers = MasterDataSupplier::all ();

        // Get unique values for filters (before applying any filters)
        // Instead of passing the filtered query, we'll pass the project ID and type
        $uniqueValues = $this->getUniqueValues ( $id_proyek, $tipe );

        return view ( "dashboard.atb.atb", [ 
            "proyek"             => $proyek,
            "proyeks"            => $proyeks,
            "spbs"               => $spbs,
            "tipe"               => $tipe,
            "TableData"          => $TableData,
            "suppliers"          => $masterDataSuppliers,
            "spareparts"         => $spareparts,
            "kategoriSpareparts" => $kategoriSpareparts,
            "headerPage"         => $proyek->nama,
            "page"               => $pageTitle,
            "search"             => $search,
            "uniqueValues"       => $uniqueValues,
        ] );
    }

    /**
     * Apply all filters from request to the query
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     * @param Request $request HTTP request
     */
    private function applyFilters ( $query, $request )
    {
        // Define filter mappings for different filter types
        $filterMap = [ 
            'nomor_spb'    => [ 'relation' => 'spb', 'field' => 'nomor' ],
            'tanggal'      => [ 'field' => 'tanggal', 'type' => 'date' ],
            'kode'         => [ 'relation' => 'masterDataSparepart.kategoriSparepart', 'field' => 'nama', 'special' => 'kode' ],
            'supplier'     => [ 'relation' => 'masterDataSupplier', 'field' => 'nama' ],
            'sparepart'    => [ 'relation' => 'masterDataSparepart', 'field' => 'nama' ],
            'merk'         => [ 'relation' => 'masterDataSparepart', 'field' => 'merk' ],
            'part_number'  => [ 'relation' => 'masterDataSparepart', 'field' => 'part_number' ],
            'quantity'     => [ 'field' => 'quantity', 'type' => 'numeric' ],
            'satuan'       => [ 'special' => 'satuan' ],
            'harga'        => [ 'field' => 'harga', 'type' => 'numeric' ],
            'jumlah_harga' => [ 'special' => 'jumlah_harga' ],
            'ppn'          => [ 'special' => 'ppn' ],
            'bruto'        => [ 'special' => 'bruto' ],
            'asal_proyek'  => [ 'relation' => 'asalProyek', 'field' => 'nama' ],
        ];

        // Loop through all possible filters
        foreach ( $filterMap as $param => $info )
        {
            // Check if filter is applied in the request
            $paramName = "selected_$param";
            if ( ! $request->has ( $paramName ) )
            {
                continue; // Skip if filter not applied
            }

            $selectedValues = $this->getSelectedValues ( $request->input ( $paramName ) );
            if ( empty ( $selectedValues ) )
            {
                continue; // Skip if no values selected
            }

            // Handle special cases
            if ( isset ( $info[ 'special' ] ) )
            {
                switch ($info[ 'special' ])
                {
                    case 'kode':
                        $this->applyKodeFilter ( $query, $selectedValues );
                        break;
                    case 'satuan':
                        $this->applySatuanFilter ( $query, $selectedValues );
                        break;
                    case 'jumlah_harga':
                        $this->applyCalculatedFilter ( $query, $selectedValues, 'quantity * harga' );
                        break;
                    case 'ppn':
                        $this->applyCalculatedFilter ( $query, $selectedValues, 'quantity * harga * 0.11' );
                        break;
                    case 'bruto':
                        $this->applyCalculatedFilter ( $query, $selectedValues, 'quantity * harga * 1.11' );
                        break;
                }
                continue;
            }

            // Handle date type
            if ( isset ( $info[ 'type' ] ) && $info[ 'type' ] === 'date' )
            {
                $this->applyDateFilter ( $query, $selectedValues, $info[ 'field' ] );
                continue;
            }

            // Handle numeric type
            if ( isset ( $info[ 'type' ] ) && $info[ 'type' ] === 'numeric' )
            {
                $this->applyNumericFilter ( $query, $selectedValues, $info[ 'field' ] );
                continue;
            }

            // Handle relation-based filters
            if ( isset ( $info[ 'relation' ] ) )
            {
                $this->applyRelationFilter ( $query, $selectedValues, $info[ 'relation' ], $info[ 'field' ] );
            }
            else if ( isset ( $info[ 'field' ] ) )
            {
                // Direct field filter
                $this->applyDirectFilter ( $query, $selectedValues, $info[ 'field' ] );
            }
        }
    }

    /**
     * Apply filter for a relation field
     */
    private function applyRelationFilter ( $query, $selectedValues, $relation, $field )
    {
        $hasEmptyFilter = in_array ( 'Empty/Null', $selectedValues );
        $nonEmptyValues = array_filter ( $selectedValues, fn ( $v ) => $v !== 'Empty/Null' );

        if ( $hasEmptyFilter )
        {
            $query->where ( function ($q) use ($relation, $field, $nonEmptyValues)
            {
                $q->whereHas ( $relation, function ($subQ) use ($field, $nonEmptyValues)
                {
                    if ( ! empty ( $nonEmptyValues ) )
                    {
                        $subQ->whereIn ( $field, $nonEmptyValues );
                    }
                } )->orWhereDoesntHave ( $relation );
            } );
        }
        else
        {
            $query->whereHas ( $relation, function ($q) use ($field, $nonEmptyValues)
            {
                $q->whereIn ( $field, $nonEmptyValues );
            } );
        }
    }

    /**
     * Apply direct field filter (non-relation)
     */
    private function applyDirectFilter ( $query, $selectedValues, $field )
    {
        $hasEmptyFilter = in_array ( 'Empty/Null', $selectedValues );
        $nonEmptyValues = array_filter ( $selectedValues, fn ( $v ) => $v !== 'Empty/Null' );

        if ( $hasEmptyFilter )
        {
            $query->where ( function ($q) use ($field, $nonEmptyValues)
            {
                $q->whereNull ( $field )
                    ->orWhere ( $field, '' )
                    ->orWhere ( $field, '-' );

                if ( ! empty ( $nonEmptyValues ) )
                {
                    $q->orWhereIn ( $field, $nonEmptyValues );
                }
            } );
        }
        else
        {
            $query->whereIn ( $field, $nonEmptyValues );
        }
    }

    /**
     * Apply kode filter (special case combining kode and nama)
     */
    private function applyKodeFilter ( $query, $selectedValues )
    {
        $hasEmptyFilter = in_array ( 'Empty/Null', $selectedValues );
        $nonEmptyValues = array_filter ( $selectedValues, fn ( $v ) => $v !== 'Empty/Null' );

        if ( $hasEmptyFilter )
        {
            $query->where ( function ($q) use ($nonEmptyValues)
            {
                $q->whereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq) use ($nonEmptyValues)
                {
                    if ( ! empty ( $nonEmptyValues ) )
                    {
                        $sq->whereIn ( DB::raw ( "CONCAT(kode, ': ', nama)" ), $nonEmptyValues );
                    }
                } )->orWhereDoesntHave ( 'masterDataSparepart.kategoriSparepart' );
            } );
        }
        else
        {
            $query->whereHas ( 'masterDataSparepart.kategoriSparepart', function ($q) use ($nonEmptyValues)
            {
                $q->whereIn ( DB::raw ( "CONCAT(kode, ': ', nama)" ), $nonEmptyValues );
            } );
        }
    }

    /**
     * Apply satuan filter (special case checking in multiple relations)
     */
    private function applySatuanFilter ( $query, $selectedValues )
    {
        $hasEmptyFilter = in_array ( 'Empty/Null', $selectedValues );
        $nonEmptyValues = array_filter ( $selectedValues, fn ( $v ) => $v !== 'Empty/Null' );

        if ( $hasEmptyFilter )
        {
            $query->where ( function ($q) use ($nonEmptyValues)
            {
                // Check in detailSpb
                $q->whereHas ( 'detailSpb', function ($sq) use ($nonEmptyValues)
                {
                    if ( ! empty ( $nonEmptyValues ) )
                    {
                        $sq->whereIn ( 'satuan', $nonEmptyValues );
                    }
                } )
                    // Check in saldo
                    ->orWhereHas ( 'saldo', function ($sq) use ($nonEmptyValues)
                    {
                        if ( ! empty ( $nonEmptyValues ) )
                        {
                            $sq->whereIn ( 'satuan', $nonEmptyValues );
                        }
                    } )
                    // No satuan
                    ->orWhereDoesntHave ( 'detailSpb' )
                    ->orWhereDoesntHave ( 'saldo' );
            } );
        }
        else
        {
            $query->where ( function ($q) use ($nonEmptyValues)
            {
                $q->whereHas ( 'detailSpb', function ($sq) use ($nonEmptyValues)
                {
                    $sq->whereIn ( 'satuan', $nonEmptyValues );
                } )->orWhereHas ( 'saldo', function ($sq) use ($nonEmptyValues)
                {
                    $sq->whereIn ( 'satuan', $nonEmptyValues );
                } );
            } );
        }
    }

    /**
     * Apply date filter with specific handling for date ranges
     */
    private function applyDateFilter ( $query, $selectedValues, $field )
    {
        $query->where ( function ($q) use ($selectedValues, $field)
        {
            // Track range conditions
            $rangeConditions = [ 
                'gt' => null,
                'lt' => null
            ];

            foreach ( $selectedValues as $value )
            {
                if ( $value === 'Empty/Null' )
                {
                    $q->orWhereNull ( $field );
                }
                else if ( strpos ( $value, 'exact:' ) === 0 )
                {
                    $date = substr ( $value, 6 );
                    $q->orWhereRaw ( "DATE($field) = ?", [ $date ] );
                }
                else if ( strpos ( $value, 'gt:' ) === 0 )
                {
                    $rangeConditions[ 'gt' ] = substr ( $value, 3 );
                }
                else if ( strpos ( $value, 'lt:' ) === 0 )
                {
                    $rangeConditions[ 'lt' ] = substr ( $value, 3 );
                }
                else if ( preg_match ( '/^(\d{4}-\d{2}-\d{2})\.\.(\d{4}-\d{2}-\d{2})$/', $value, $matches ) )
                {
                    $q->orWhereBetween ( $field, [ $matches[ 1 ], $matches[ 2 ] ] );
                }
            }

            // Apply range filter if gt or lt exists
            if ( $rangeConditions[ 'gt' ] || $rangeConditions[ 'lt' ] )
            {
                $q->orWhere ( function ($rangeQ) use ($rangeConditions, $field)
                {
                    if ( $rangeConditions[ 'gt' ] )
                    {
                        $rangeQ->whereRaw ( "DATE($field) >= ?", [ $rangeConditions[ 'gt' ] ] );
                    }
                    if ( $rangeConditions[ 'lt' ] )
                    {
                        $rangeQ->whereRaw ( "DATE($field) <= ?", [ $rangeConditions[ 'lt' ] ] );
                    }
                } );
            }
        } );
    }

    /**
     * Apply numeric filter with gt, lt, exact handling
     */
    private function applyNumericFilter ( $query, $selectedValues, $field )
    {
        $hasEmptyFilter = in_array ( 'Empty/Null', $selectedValues );
        $gtValue        = null;
        $ltValue        = null;
        $exactValues    = [];

        foreach ( $selectedValues as $value )
        {
            if ( $value === 'Empty/Null' )
            {
                continue; // Handled separately
            }
            else if ( strpos ( $value, 'gt:' ) === 0 )
            {
                $gtValue = (float) substr ( $value, 3 );
            }
            else if ( strpos ( $value, 'lt:' ) === 0 )
            {
                $ltValue = (float) substr ( $value, 3 );
            }
            else if ( strpos ( $value, 'exact:' ) === 0 )
            {
                $exactValues[] = (float) substr ( $value, 6 );
            }
            else if ( is_numeric ( $value ) )
            {
                $exactValues[] = (float) $value;
            }
        }

        $query->where ( function ($q) use ($hasEmptyFilter, $gtValue, $ltValue, $exactValues, $field)
        {
            if ( $hasEmptyFilter )
            {
                $q->orWhereNull ( $field )->orWhere ( $field, 0 );
            }

            if ( $gtValue !== null && $ltValue !== null )
            {
                $q->orWhereBetween ( $field, [ $gtValue, $ltValue ] );
            }
            else
            {
                if ( $gtValue !== null )
                {
                    $q->orWhere ( $field, '>=', $gtValue );
                }
                if ( $ltValue !== null )
                {
                    $q->orWhere ( $field, '<=', $ltValue );
                }
            }

            if ( ! empty ( $exactValues ) )
            {
                $q->orWhereIn ( $field, $exactValues );
            }
        } );
    }

    /**
     * Apply calculated field filter (jumlah_harga, ppn, bruto)
     */
    private function applyCalculatedFilter ( $query, $selectedValues, $formula )
    {
        $hasEmptyFilter = in_array ( 'Empty/Null', $selectedValues );
        $gtValue        = null;
        $ltValue        = null;
        $exactValues    = [];

        foreach ( $selectedValues as $value )
        {
            if ( $value === 'Empty/Null' )
            {
                continue; // Handled separately
            }
            else if ( strpos ( $value, 'gt:' ) === 0 )
            {
                $gtValue = (float) substr ( $value, 3 );
            }
            else if ( strpos ( $value, 'lt:' ) === 0 )
            {
                $ltValue = (float) substr ( $value, 3 );
            }
            else if ( strpos ( $value, 'exact:' ) === 0 )
            {
                $exactValues[] = (float) substr ( $value, 6 );
            }
            else if ( is_numeric ( $value ) )
            {
                $exactValues[] = (float) $value;
            }
        }

        $query->where ( function ($q) use ($hasEmptyFilter, $gtValue, $ltValue, $exactValues, $formula)
        {
            if ( $hasEmptyFilter )
            {
                $q->orWhereNull ( DB::raw ( $formula ) )->orWhere ( DB::raw ( $formula ), 0 );
            }

            if ( $gtValue !== null && $ltValue !== null )
            {
                $q->orWhereBetween ( DB::raw ( $formula ), [ $gtValue, $ltValue ] );
            }
            else
            {
                if ( $gtValue !== null )
                {
                    $q->orWhere ( DB::raw ( $formula ), '>=', $gtValue );
                }
                if ( $ltValue !== null )
                {
                    $q->orWhere ( DB::raw ( $formula ), '<=', $ltValue );
                }
            }

            if ( ! empty ( $exactValues ) )
            {
                $q->orWhereIn ( DB::raw ( $formula ), $exactValues );
            }
        } );
    }

    /**
     * Extract unique values for all filterable fields
     * 
     * @param int $id_proyek Project ID
     * @param string $tipe ATB type
     * @return array Associative array of unique values by field
     */
    private function getUniqueValues ( $id_proyek, $tipe )
    {
        // Create a new base query for all ATB records of this type for this project
        $baseQuery = ATB::where ( 'id_proyek', $id_proyek )
            ->where ( 'tipe', $tipe );

        // Get all ATB IDs from the query to use in subqueries for better performance
        $atbIds = $baseQuery->pluck ( 'id' )->toArray ();
        if ( empty ( $atbIds ) )
        {
            return [ 
                'nomor_spb'    => [],
                'tanggal'      => [],
                'kode'         => [],
                'supplier'     => [],
                'sparepart'    => [],
                'merk'         => [],
                'part_number'  => [],
                'quantity'     => [],
                'satuan'       => [],
                'harga'        => [],
                'jumlah_harga' => [],
                'ppn'          => [],
                'bruto'        => [],
                'asal_proyek'  => [],
            ];
        }

        // Get unique SPB numbers
        $nomorSpb = ATB::whereIn ( 'id', $atbIds )
            ->whereNotNull ( 'id_spb' )
            ->with ( 'spb' )
            ->get ()
            ->map ( function ($atb)
            {
                return $atb->spb->nomor ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique dates in formatted form
        $dates = ATB::whereIn ( 'id', $atbIds )
            ->whereNotNull ( 'tanggal' )
            ->orderBy ( 'tanggal' )
            ->pluck ( 'tanggal' )
            ->map ( function ($date)
            {
                return date ( 'Y-m-d', strtotime ( $date ) );
            } )
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique kategori sparepart combinations (kode: nama)
        $kategoriSpareparts = ATB::whereIn ( 'id', $atbIds )
            ->whereHas ( 'masterDataSparepart.kategoriSparepart' )
            ->with ( 'masterDataSparepart.kategoriSparepart' )
            ->get ()
            ->map ( function ($atb)
            {
                if ( $atb->masterDataSparepart && $atb->masterDataSparepart->kategoriSparepart )
                {
                    $kat = $atb->masterDataSparepart->kategoriSparepart;
                    return $kat->kode . ': ' . $kat->nama;
                }
                return null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique suppliers
        $suppliers = ATB::whereIn ( 'id', $atbIds )
            ->whereHas ( 'masterDataSupplier' )
            ->with ( 'masterDataSupplier' )
            ->get ()
            ->map ( function ($atb)
            {
                return $atb->masterDataSupplier->nama ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique spareparts
        $spareparts = ATB::whereIn ( 'id', $atbIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($atb)
            {
                return $atb->masterDataSparepart->nama ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique merks
        $merks = ATB::whereIn ( 'id', $atbIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($atb)
            {
                return $atb->masterDataSparepart->merk ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique part numbers
        $partNumbers = ATB::whereIn ( 'id', $atbIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($atb)
            {
                return $atb->masterDataSparepart->part_number ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique quantities (rounded to the nearest whole number)
        $quantities = ATB::whereIn ( 'id', $atbIds )
            ->whereNotNull ( 'quantity' )
            ->pluck ( 'quantity' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Get unique satuan values from both DetailSPB and Saldo
        $satuanValues = [];

        // From DetailSPB
        $satuanFromDetailSpb = ATB::whereIn ( 'id', $atbIds )
            ->whereNotNull ( 'id_detail_spb' )
            ->with ( 'detailSpb' )
            ->get ()
            ->map ( function ($atb)
            {
                return $atb->detailSpb->satuan ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // From Saldo related to ATB
        $satuanFromSaldo = ATB::whereIn ( 'id', $atbIds )
            ->whereHas ( 'saldo' )
            ->with ( 'saldo' )
            ->get ()
            ->map ( function ($atb)
            {
                return $atb->saldo->satuan ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Combine values from DetailSPB and Saldo only
        $satuanValues = array_unique ( array_merge ( $satuanFromDetailSpb, $satuanFromSaldo ) );
        sort ( $satuanValues );

        // Get unique harga values (rounded to the nearest 1000)
        $hargaValues = ATB::whereIn ( 'id', $atbIds )
            ->whereNotNull ( 'harga' )
            ->pluck ( 'harga' )
            ->map ( function ($harga)
            {
                return round ( $harga / 1000 ) * 1000; // Round to nearest 1000 for better grouping
            } )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Calculate jumlah_harga, ppn, and bruto for uniqueness
        $calculatedValues = ATB::whereIn ( 'id', $atbIds )
            ->whereNotNull ( 'quantity' )
            ->whereNotNull ( 'harga' )
            ->select ( 'id', 'quantity', 'harga' )
            ->get ()
            ->map ( function ($atb)
            {
                $jumlahHarga = $atb->quantity * $atb->harga;
                $ppn   = $jumlahHarga * 0.11;
                $bruto = $jumlahHarga * 1.11;

                return [ 
                    'jumlah_harga' => round ( $jumlahHarga / 1000 ) * 1000, // Round to nearest 1000
                    'ppn'          => round ( $ppn / 1000 ) * 1000,
                    'bruto'        => round ( $bruto / 1000 ) * 1000,
                ];
            } )
            ->toArray ();

        $jumlahHargaValues = array_column ( $calculatedValues, 'jumlah_harga' );
        $jumlahHargaValues = array_unique ( $jumlahHargaValues );
        sort ( $jumlahHargaValues );

        $ppnValues = array_column ( $calculatedValues, 'ppn' );
        $ppnValues = array_unique ( $ppnValues );
        sort ( $ppnValues );

        $brutoValues = array_column ( $calculatedValues, 'bruto' );
        $brutoValues = array_unique ( $brutoValues );
        sort ( $brutoValues );

        // Get unique asal_proyek values
        $asalProyek = ATB::whereIn ( 'id', $atbIds )
            ->whereNotNull ( 'id_asal_proyek' )
            ->with ( 'asalProyek' )
            ->get ()
            ->map ( function ($atb)
            {
                return $atb->asalProyek->nama ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Return all unique values
        return [ 
            'nomor_spb'    => $nomorSpb,
            'tanggal'      => $dates,
            'kode'         => $kategoriSpareparts,
            'supplier'     => $suppliers,
            'sparepart'    => $spareparts,
            'merk'         => $merks,
            'part_number'  => $partNumbers,
            'quantity'     => $quantities,
            'satuan'       => $satuanValues,
            'harga'        => $hargaValues,
            'jumlah_harga' => $jumlahHargaValues,
            'ppn'          => $ppnValues,
            'bruto'        => $brutoValues,
            'asal_proyek'  => $asalProyek,
        ];
    }

    public function getlinkSpbDetailSpbs ( $id )
    {
        $SPB = SPB::with ( [ 
            "linkSpbDetailSpb.detailSpb.MasterDataSparepart",
            "linkSpbDetailSpb.detailSpb.linkSpbDetailSpb.spb",
        ] )->find ( $id );

        $DetailSPB = [];

        foreach ( $SPB->linkSpbDetailSpb as $item )
        {
            $DetailSPB[] = $item->detailSpb;
        }

        $html = view ( 'dashboard.atb.partials.spb-details-table', [ 'spbDetails' => $DetailSPB ] )->render ();

        return response ()->json ( [ 'html' => $html ] );
    }

    public function store ( Request $request )
    {
        // dd ( $request->all () );
        try
        {
            DB::beginTransaction ();

            if ( $request->tipe === "hutang-unit-alat-bypass" )
            {
                // Validate request for bypass type
                $validated = $request->validate ( [ 
                    'tipe'                     => 'required|string',
                    'tanggal'                  => 'required|date',
                    'id_proyek'                => 'required|exists:proyek,id',
                    'id_master_data_supplier'  => 'required|exists:master_data_supplier,id',
                    'id_master_data_sparepart' => 'required|exists:master_data_sparepart,id',
                    'quantity'                 => 'required|integer|min:1',
                    'harga'                    => 'required|numeric|min:0',
                    'satuan'                   => 'required|string'
                ] );

                // Create ATB record for bypass type
                $atb = ATB::create ( [ 
                    'tipe'                     => 'hutang-unit-alat',
                    'tanggal'                  => $request->tanggal,
                    'quantity'                 => $request->quantity,
                    'harga'                    => $request->harga,
                    'id_proyek'                => $request->id_proyek,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart,
                    'id_master_data_supplier'  => $request->id_master_data_supplier
                ] );

                // Create corresponding Saldo record
                $saldoController = new SaldoController();
                $saldoController->store ( [ 
                    'tipe'                     => 'hutang-unit-alat',
                    'quantity'                 => $request->quantity,
                    'harga'                    => $request->harga,
                    'id_proyek'                => $request->id_proyek,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart,
                    'id_master_data_supplier'  => $request->id_master_data_supplier,
                    'id_atb'                   => $atb->id,
                    'satuan'                   => $request->satuan
                ] );

            }
            elseif ( $request->tipe === "hutang-unit-alat" )
            {

                // Update validation rule for quantity
                $validated = $request->validate ( [ 
                    'tipe'                       => 'required|string',
                    'tanggal'                    => 'required|date',
                    'id_proyek'                  => 'required|exists:proyek,id',
                    'id_spb'                     => 'required|exists:spb,id',
                    'surat_tanda_terima'         => 'required|file|mimes:pdf|max:10240',
                    'quantity'                   => 'required|array',
                    'quantity.*'                 => 'required|integer|min:0', // Allow 0 quantity
                    'id_detail_spb'              => 'required|array',
                    'id_detail_spb.*'            => 'required|exists:detail_spb,id',
                    'id_master_data_sparepart'   => 'required|array',
                    'id_master_data_sparepart.*' => 'required|exists:master_data_sparepart,id',
                    'harga'                      => 'required|array',
                    'harga.*'                    => 'required|numeric|min:0',
                    'documentation_photos'       => 'array',
                    'documentation_photos.*'     => 'array',
                    'documentation_photos.*.*'   => 'file|image|mimes:jpeg,png,jpg|max:2048',
                    'id_master_data_supplier'    => 'required|array',
                    'id_master_data_supplier.*'  => 'required|exists:master_data_supplier,id',
                    'satuan'                     => 'required|array', // New validation rule
                    'satuan.*'                   => 'required|string' // New validation rule
                ] );

                // Create base storage paths
                $folderName      = 'atb_' . date ( 'YmdHis' ) . '_' . uniqid ();
                $baseStoragePath = 'uploads/atb/' . $folderName;
                $suratPath       = $baseStoragePath . '/surat';
                Storage::disk ( 'public' )->makeDirectory ( $suratPath );

                // Handle surat_tanda_terima upload
                $suratFile     = $request->file ( 'surat_tanda_terima' );
                $originalName  = pathinfo ( $suratFile->getClientOriginalName (), PATHINFO_FILENAME );
                $extension     = $suratFile->getClientOriginalExtension ();
                $timestamp     = now ()->format ( 'Y-m-d--H-i-s' );
                $suratFileName = "{$originalName}___{$timestamp}.{$extension}";
                $suratFilePath = $suratFile->storeAs ( $suratPath, $suratFileName, 'public' );

                $saldoController = new SaldoController();
                $processedItems  = 0;
                $skippedReasons  = [];

                // Process each detail SPB item
                foreach ( $request->id_detail_spb as $index => $id_detail_spb )
                {
                    // Get the DetailSPB record
                    $detailSpb         = DetailSPB::find ( $id_detail_spb );
                    $requestedQuantity = intval ( $request->quantity[ $index ] );

                    // Skip if quantity is invalid or item has no remaining quantity
                    if ( $requestedQuantity < 0 )
                    {
                        $skippedReasons[] = "Index $index: Invalid quantity";
                        continue;
                    }

                    // Skip validation for documentation photos if quantity is 0
                    if ( $requestedQuantity > 0 )
                    {
                        // Check for documentation photos with both current index and index+1
                        $hasPhotos = isset ( $request->file ( 'documentation_photos' )[ $index ] ) ||
                            isset ( $request->file ( 'documentation_photos' )[ $index + 1 ] );

                        if ( ! $hasPhotos )
                        {
                            $skippedReasons[] = "Index $index: Missing photos for non-zero quantity";
                            continue;
                        }
                    }

                    // Only continue processing if quantity is greater than 0
                    if ( $requestedQuantity > 0 )
                    {
                        // Validate remaining quantity
                        if ( $detailSpb->quantity_belum_diterima <= 0 )
                        {
                            $skippedReasons[] = "Index $index: No remaining quantity";
                            continue;
                        }

                        // Validate that we have all required data for this index
                        if (
                            ! isset ( $request->id_master_data_sparepart[ $index ] ) ||
                            ! isset ( $request->harga[ $index ] ) ||
                            ! isset ( $request->id_master_data_supplier[ $index ] )
                        )
                        {
                            if ( ! isset ( $request->id_master_data_sparepart[ $index ] ) )
                            {
                                $skippedReasons[] = "Index $index: Missing sparepart data";
                            }
                            if ( ! isset ( $request->harga[ $index ] ) )
                            {
                                $skippedReasons[] = "Index $index: Missing harga";
                            }
                            if ( ! isset ( $request->id_master_data_supplier[ $index ] ) )
                            {
                                $skippedReasons[] = "Index $index: Missing supplier";
                            }
                            if ( ! isset ( $request->file ( 'documentation_photos' )[ $index ] ) )
                            {
                                $skippedReasons[] = "Index $index: Missing photos";
                            }
                            continue;
                        }

                        // Create documentation folder for this item
                        $docPath = $baseStoragePath . '/dokumentasi_' . uniqid ();
                        Storage::disk ( 'public' )->makeDirectory ( $docPath );

                        // Try to get photos from either current index or index+1
                        $photos = $request->file ( 'documentation_photos' )[ $index ] ??
                            $request->file ( 'documentation_photos' )[ $index + 1 ] ??
                            null;

                        if ( $photos )
                        {
                            foreach ( $photos as $photo )
                            {
                                $photoName      = pathinfo ( $photo->getClientOriginalName (), PATHINFO_FILENAME );
                                $photoExt       = $photo->getClientOriginalExtension ();
                                $photoTimestamp = now ()->format ( 'Y-m-d--H-i-s' );
                                $fileName       = "{$photoName}___{$photoTimestamp}.{$photoExt}";
                                $photo->storeAs ( $docPath, $fileName, 'public' );
                            }
                        }

                        // Update quantity_belum_diterima
                        $detailSpb->reduceQuantityBelumDiterima ( $requestedQuantity );

                        // Create ATB record
                        $atb = ATB::create ( [ 
                            'tipe'                     => $request->tipe,
                            'dokumentasi_foto'         => $docPath,
                            'surat_tanda_terima'       => $suratFilePath,
                            'tanggal'                  => $request->tanggal,
                            'quantity'                 => $requestedQuantity,
                            'harga'                    => $request->harga[ $index ],
                            'id_proyek'                => $request->id_proyek,
                            'id_spb'                   => $request->id_spb,
                            'id_detail_spb'            => $id_detail_spb,
                            'id_master_data_sparepart' => $request->id_master_data_sparepart[ $index ],
                            'id_master_data_supplier'  => $request->id_master_data_supplier[ $index ]
                        ] );

                        // Create corresponding Saldo record
                        $saldoController->store ( [ 
                            'tipe'                     => $request->tipe,
                            'quantity'                 => $requestedQuantity,
                            'harga'                    => $request->harga[ $index ],
                            'id_proyek'                => $request->id_proyek,
                            'id_asal_proyek'           => null, // Add this line
                            'id_spb'                   => $request->id_spb,
                            'id_master_data_sparepart' => $request->id_master_data_sparepart[ $index ],
                            'id_master_data_supplier'  => $request->id_master_data_supplier[ $index ],
                            'id_atb'                   => $atb->id, // New column
                            'satuan'                   => $request->satuan[ $index ] // New column
                        ] );

                        $processedItems++;
                    }
                }

                if ( $processedItems === 0 )
                {
                    throw new \Exception( 'Tidak ada item yang valid untuk diproses. Alasan: ' . implode ( ', ', $skippedReasons ) );
                }

            }

            if ( $request->tipe == "panjar-proyek" || $request->tipe === "panjar-unit-alat" )
            {
                $validated = $request->validate ( [ 
                    'tipe'                     => 'required|string',
                    'tanggal'                  => 'required|date',
                    'id_proyek'                => 'required|exists:proyek,id',
                    'id_kategori_sparepart'    => 'required|exists:kategori_sparepart,id',
                    'id_master_data_sparepart' => 'required|exists:master_data_sparepart,id',
                    'id_master_data_supplier'  => 'required|exists:master_data_supplier,id', // Add this line
                    'quantity'                 => 'required|integer|min:1',
                    'harga'                    => 'required|numeric|min:0',
                    'satuan'                   => 'required|string', // Add this line
                    'dokumentasi'              => 'required|array',
                    'dokumentasi.*'            => 'file|image|mimes:jpeg,png,jpg|max:2048'
                ] );

                // Create base storage paths
                $folderName      = 'atb_' . date ( 'YmdHis' ) . '_' . uniqid ();
                $baseStoragePath = 'uploads/atb/' . $folderName;
                $docPath         = $baseStoragePath . '/dokumentasi';
                Storage::disk ( 'public' )->makeDirectory ( $docPath );

                // Handle dokumentasi upload
                $photos = $request->file ( 'dokumentasi' );
                foreach ( $photos as $photo )
                {
                    $photoName      = pathinfo ( $photo->getClientOriginalName (), PATHINFO_FILENAME );
                    $photoExt       = $photo->getClientOriginalExtension ();
                    $photoTimestamp = now ()->format ( 'Y-m-d--H-i-s' );
                    $fileName       = "{$photoName}___{$photoTimestamp}.{$photoExt}";
                    $photo->storeAs ( $docPath, $fileName, 'public' );
                }

                // Create ATB record
                $atb = ATB::create ( [ 
                    'tipe'                     => $request->tipe,
                    'dokumentasi_foto'         => $docPath,
                    'tanggal'                  => $request->tanggal,
                    'quantity'                 => $request->quantity,
                    'harga'                    => $request->harga,
                    'id_proyek'                => $request->id_proyek,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart,
                    'id_master_data_supplier'  => $request->id_master_data_supplier
                ] );

                // Create corresponding Saldo record
                $saldoController = new SaldoController();
                $saldoController->store ( [ 
                    'tipe'                     => $request->tipe,
                    'quantity'                 => $request->quantity,
                    'harga'                    => $request->harga,
                    'satuan'                   => $request->satuan, // Add this line
                    'id_proyek'                => $request->id_proyek,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart,
                    'id_master_data_supplier'  => $request->id_master_data_supplier, // Add this line
                    'id_atb'                   => $atb->id
                ] );

                // Remove or comment out this line as it's not needed
                // dd ( $request->all () );
            }

            DB::commit ();
            return back ()->with ( 'success', 'Data ATB dan Saldo berhasil disimpan' );

        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            if ( isset ( $baseStoragePath ) )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $baseStoragePath );
            }
            return back ()->withErrors ( [ 'error' => 'Gagal menyimpan data ATB dan Saldo: ' . $e->getMessage () ] )->withInput ();
        }
    }

    public function destroy ( $id )
    {
        try
        {
            DB::beginTransaction ();

            $atb = ATB::findOrFail ( $id );

            if ( $atb->tipe === 'panjar-proyek' || $atb->tipe === 'panjar-unit-alat' )
            {
                // For panjar types, simply delete the ATB and associated records

                // Delete associated files
                if ( $atb->dokumentasi_foto )
                {
                    Storage::disk ( 'public' )->deleteDirectory ( $atb->dokumentasi_foto );
                }

                // Delete the associated Saldo record
                $saldo = Saldo::where ( 'id_atb', $atb->id )->first ();
                if ( $saldo )
                {
                    $saldoController = new SaldoController();
                    $saldoController->destroy ( $saldo->id );
                }

                // Delete the ATB record
                $atb->delete ();
            }

            if ( $atb->tipe === 'hutang-unit-alat' )
            {
                $suratTandaTerima = $atb->surat_tanda_terima;

                if ( $atb->id_spb )
                {
                    // Find all ATB records with the same Surat Tanda Terima
                    $atbs = ATB::where ( 'surat_tanda_terima', $suratTandaTerima )->get ();
                }
                else
                {
                    // If no SPB, only handle current ATB
                    $atbs = collect ( [ $atb ] );
                }

                $saldoController = new SaldoController();

                foreach ( $atbs as $atb )
                {
                    // Delete associated files
                    if ( $atb->dokumentasi_foto )
                    {
                        Storage::disk ( 'public' )->deleteDirectory ( $atb->dokumentasi_foto );
                    }

                    // Restore quantity_belum_diterima for the corresponding DetailSPB only if SPB exists
                    if ( $atb->id_spb && $atb->id_detail_spb )
                    {
                        $detailSpb = DetailSPB::find ( $atb->id_detail_spb );
                        if ( $detailSpb )
                        {
                            $detailSpb->increaseQuantityBelumDiterima ( $atb->quantity );
                        }
                    }

                    // Delete the associated Saldo record
                    $saldo = Saldo::where ( 'id_atb', $atb->id )->first ();
                    if ( $saldo )
                    {
                        $saldoController->destroy ( $saldo->id );
                    }

                    // Delete the ATB record
                    $atb->delete ();
                }

                // Delete the shared surat_tanda_terima file if it exists
                if ( $suratTandaTerima )
                {
                    // Get the main ATB folder path by extracting the parent directory path
                    $mainFolderPath = dirname ( dirname ( $suratTandaTerima ) );

                    // Delete the entire ATB folder and all its contents
                    Storage::disk ( 'public' )->deleteDirectory ( $mainFolderPath );
                }
            }

            DB::commit ();

            return back ()->with ( 'success', 'Data ATB berhasil dihapus' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return back ()->withErrors ( [ 'error' => 'Gagal menghapus data ATB: ' . $e->getMessage () ] );
        }
    }

    public function getStt ( $id )
    {
        try
        {
            $atb = ATB::findOrFail ( $id );

            // Check if STT exists
            if ( ! $atb->surat_tanda_terima || ! file_exists ( storage_path ( 'app/public/' . $atb->surat_tanda_terima ) ) )
            {
                return response ()->json ( [ 
                    'success' => false,
                    'message' => 'STT tidak ditemukan'
                ], 404 );
            }

            // Return PDF URL
            return response ()->json ( [ 
                'success' => true,
                'pdf_url' => asset ( 'storage/' . $atb->surat_tanda_terima )
            ] );
        }
        catch ( \Exception $e )
        {
            return response ()->json ( [ 
                'success' => false,
                'message' => 'Error fetching STT: ' . $e->getMessage ()
            ], 500 );
        }
    }

    public function getDokumentasi ( $id )
    {
        $atb         = ATB::findOrFail ( $id );
        $dokumentasi = [];

        // Update the path to check the storage path instead of public path
        $storagePath = storage_path ( 'app/public/' . $atb->dokumentasi_foto );

        if ( $atb->dokumentasi_foto && is_dir ( $storagePath ) )
        {
            // Get all image files from directory
            $files = glob ( $storagePath . '/*.{jpg,jpeg,png}', GLOB_BRACE );

            foreach ( $files as $file )
            {
                // Convert full system path to relative public path
                $relativePath  = 'storage/' . $atb->dokumentasi_foto . '/' . basename ( $file );
                $dokumentasi[] = $relativePath;
            }
        }

        return response ()->json ( [ 
            'dokumentasi' => $dokumentasi
        ] );
    }

    public function acceptMutasi ( Request $request, $id )
    {
        try
        {
            // Update validation to include tanggal_terima

            $validated = $request->validate ( [ 
                'id_atb'         => 'required|exists:atb,id',
                'quantity'       => 'required|integer|min:1',
                'tanggal_terima' => 'required|date',
                'dokumentasi'    => 'required|array',
                'dokumentasi.*'  => 'required|file|image|mimes:jpeg,png,jpg|max:2048'
            ] );

            DB::beginTransaction ();

            // Find the ATB record to update
            $atb = ATB::with ( [ 'apbMutasi.saldo' ] )->findOrFail ( $request->id_atb );

            if ( $atb->tipe !== 'mutasi-proyek' )
            {
                throw new \Exception( 'This operation is only valid for mutation type ATB' );
            }

            // Get the linked APB Mutasi and its Saldo
            $apbMutasi = $atb->apbMutasi;
            $saldo     = $apbMutasi->saldo;

            if ( ! $saldo )
            {
                throw new \Exception( 'No saldo record found for this mutation' );
            }

            // Validate quantity against APB Mutasi quantity
            if ( $request->quantity > $apbMutasi->quantity )
            {
                throw new \Exception( 'Quantity cannot exceed the mutated quantity' );
            }

            // Handle dokumentasi upload
            $folderName      = 'atb_' . date ( 'YmdHis' ) . '_' . uniqid ();
            $baseStoragePath = 'uploads/atb/' . $folderName;
            $docPath         = $baseStoragePath . '/dokumentasi';
            Storage::disk ( 'public' )->makeDirectory ( $docPath );

            // Process and store documentation photos
            if ( $request->hasFile ( 'dokumentasi' ) )
            {
                foreach ( $request->file ( 'dokumentasi' ) as $photo )
                {
                    $photoName      = pathinfo ( $photo->getClientOriginalName (), PATHINFO_FILENAME );
                    $photoExt       = $photo->getClientOriginalExtension ();
                    $photoTimestamp = now ()->format ( 'Y-m-d--H-i-s' );
                    $fileName       = "{$photoName}___{$photoTimestamp}.{$photoExt}";
                    $photo->storeAs ( $docPath, $fileName, 'public' );
                }
            }

            // Delete old dokumentasi if exists
            if ( $atb->dokumentasi_foto )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $atb->dokumentasi_foto );
            }

            // Update the existing ATB record with tanggal_terima
            $atb->update ( [ 
                'quantity'         => $request->quantity,
                'dokumentasi_foto' => $docPath,
                'tanggal'          => $request->tanggal_terima // Add this line
            ] );

            // Update or create Saldo record
            $saldoController = new SaldoController();
            $existingSaldo   = Saldo::where ( 'id_atb', $atb->id )->first ();

            if ( $existingSaldo )
            {
                $existingSaldo->update ( [ 
                    'quantity' => $request->quantity
                ] );
            }
            else
            {
                $saldoController->store ( [ 
                    'tipe'                     => 'mutasi-proyek',
                    'quantity'                 => $request->quantity,
                    'harga'                    => $atb->harga,
                    'id_proyek'                => $atb->id_proyek,
                    'id_asal_proyek'           => $atb->id_asal_proyek,
                    'id_master_data_sparepart' => $atb->id_master_data_sparepart,
                    'id_master_data_supplier'  => $atb->id_master_data_supplier,
                    'id_atb'                   => $atb->id,
                    'satuan'                   => $saldo->satuan
                ] );
            }

            $atb->apbMutasi->update ( [ 'status' => 'accepted' ] );

            $saldoAsal = Saldo::find ( $atb->apbMutasi->id_saldo );
            $saldoAsal->decrementQuantity ( $request->quantity );

            DB::commit ();
            return back ()->with ( 'success', 'Mutasi ATB berhasil diterima.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            // Clean up uploaded files if they exist
            if ( isset ( $baseStoragePath ) )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $baseStoragePath );
            }
            return back ()->withErrors ( [ 'error' => 'Gagal menerima mutasi ATB: ' . $e->getMessage () ] );
        }
    }

    public function rejectMutasi ( Request $request, $id )
    {
        try
        {
            // Add validation for tanggal_tolak
            $validated = $request->validate ( [ 
                'tanggal_tolak' => 'required|date'
            ] );

            // Find ATB first to validate it exists
            $atb = ATB::findOrFail ( $id );

            DB::beginTransaction ();

            // Update APBMutasi with status and rejection date
            $atb->apbMutasi->update ( [ 
                'status'          => 'rejected',
                'tanggal_ditolak' => $request->tanggal_tolak // Add this line
            ] );

            DB::commit ();
            return back ()->with ( 'success', 'Mutasi ATB berhasil ditolak.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            // Clean up uploaded files if they exist
            if ( isset ( $baseStoragePath ) )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $baseStoragePath );
            }
            return back ()->withErrors ( [ 'error' => 'Gagal menolak mutasi ATB: ' . $e->getMessage () ] );
        }
    }
}