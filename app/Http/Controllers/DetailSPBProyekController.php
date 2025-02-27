<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\SPB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use console;

class DetailSPBProyekController extends Controller
{
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
            // Values are separated by || instead of commas
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            console ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
    }

    /**
     * Extracts unique values for filter options
     * 
     * @param int $rkbId RKB ID
     * @return array Array of unique values for each filter field
     */
    private function getUniqueValues ( $rkbId )
    {
        // Get all SPB IDs related to this RKB
        $spbIds = RKB::findOrFail ( $rkbId )->spbs ()->pluck ( 'id_spb' );

        // Get all SPBs without filtering
        $allSpbs = SPB::with ( [ 
            'linkSpbDetailSpb.detailSPB.masterDataAlat',
            'linkSpbDetailSpb.detailSPB.masterDataSparepart.kategoriSparepart',
            'linkSpbDetailSpb.detailSPB.atbs',
            'masterDataSupplier'
        ] )
            ->where ( 'is_addendum', false )
            ->whereIn ( 'id', $spbIds )
            ->get ();

        // Extract unique values for each field
        $jenisAlat        = collect ();
        $kodeAlat         = collect ();
        $kategori         = collect ();
        $sparepart        = collect ();
        $merk             = collect ();
        $supplier         = collect ();
        $quantityPO       = collect ();
        $quantityDiterima = collect ();
        $satuan           = collect ();
        $harga            = collect ();
        $jumlahHarga      = collect ();

        foreach ( $allSpbs as $spb )
        {
            if ( $spb->masterDataSupplier )
            {
                $supplier->push ( $spb->masterDataSupplier->nama );
            }

            foreach ( $spb->linkSpbDetailSpb as $detail )
            {
                if ( $detail->detailSPB->masterDataAlat )
                {
                    $jenisAlat->push ( $detail->detailSPB->masterDataAlat->jenis_alat );
                    $kodeAlat->push ( $detail->detailSPB->masterDataAlat->kode_alat );
                }

                if ( $detail->detailSPB->masterDataSparepart )
                {
                    $sparepart->push ( $detail->detailSPB->masterDataSparepart->nama );
                    $merk->push ( $detail->detailSPB->masterDataSparepart->merk );

                    if ( $detail->detailSPB->masterDataSparepart->kategoriSparepart )
                    {
                        $kategori->push ( $detail->detailSPB->masterDataSparepart->kategoriSparepart->nama );
                    }
                }

                $quantityPO->push ( (string) $detail->detailSPB->quantity_po );
                $quantityDiterima->push ( (string) $detail->detailSPB->atbs->sum ( 'quantity' ) );
                $satuan->push ( $detail->detailSPB->satuan );
                $harga->push ( (string) $detail->detailSPB->harga );
                $jumlahHarga->push ( (string) ( $detail->detailSPB->harga * $detail->detailSPB->quantity_po ) );
            }
        }

        return [ 
            'uniqueJenisAlat'        => $jenisAlat->filter ()->unique ()->sort ()->values (),
            'uniqueKodeAlat'         => $kodeAlat->filter ()->unique ()->sort ()->values (),
            'uniqueKategori'         => $kategori->filter ()->unique ()->sort ()->values (),
            'uniqueSparepart'        => $sparepart->filter ()->unique ()->sort ()->values (),
            'uniqueMerk'             => $merk->filter ()->unique ()->sort ()->values (),
            'uniqueSupplier'         => $supplier->filter ()->unique ()->sort ()->values (),
            'uniqueQuantityPO'       => $quantityPO->filter ()->unique ()->sort ()->values (),
            'uniqueQuantityDiterima' => $quantityDiterima->filter ()->unique ()->sort ()->values (),
            'uniqueSatuan'           => $satuan->filter ()->unique ()->sort ()->values (),
            'uniqueHarga'            => $harga->filter ()->unique ()->sort ()->values (),
            'uniqueJumlahHarga'      => $jumlahHarga->filter ()->unique ()->sort ()->values (),
        ];
    }

    /**
     * Main index method to show and filter SPB data
     * 
     * @param Request $request HTTP request
     * @param int $id RKB ID
     * @return \Illuminate\View\View
     */
    public function index ( Request $request, $id )
    {
        $rkb     = RKB::findOrFail ( $id );
        $perPage = $request->input ( 'per_page', 10 );

        // Get all SPBs related to this RKB
        $spbIds = $rkb->spbs ()->pluck ( 'id_spb' )->toArray (); // Convert to array for proper use

        if ( empty ( $spbIds ) )
        {
            // Handle case with no related SPBs
            $spbIds = [ 0 ]; // Use a dummy ID that won't match anything to avoid SQL error
            console ( "No SPB IDs found for RKB ID: $id" );
        }

        // Get unique values for filters first
        $uniqueValuesData = $this->getUniqueValues ( $id );

        // Store filter criteria for applying to detail records later
        $filterParams = [];
        foreach ( $request->all () as $key => $value )
        {
            if ( strpos ( $key, 'selected_' ) === 0 )
            {
                $paramName                  = str_replace ( 'selected_', '', $key );
                $filterParams[ $paramName ] = $this->getSelectedValues ( $value );

                console ( "Filter parameter: {$key} = {$value}" );
                console ( "Decoded values: " . json_encode ( $filterParams[ $paramName ], JSON_UNESCAPED_UNICODE ) );
            }
        }

        // Create a base query for SPBs with explicit boolean value
        $query = SPB::with ( [ 
            'linkSpbDetailSpb.detailSPB.masterDataAlat',
            'linkSpbDetailSpb.detailSPB.masterDataSparepart.kategoriSparepart',
            'linkSpbDetailSpb.detailSPB.atbs',
            'masterDataSupplier',
            'originalSpb.addendums',
        ] )
            ->where ( 'is_addendum', '=', false ) // Explicitly using '=' operator for clarity
            ->whereIn ( 'id', $spbIds );

        // Log filter parameters for debugging
        foreach ( $request->all () as $key => $value )
        {
            if ( strpos ( $key, 'selected_' ) === 0 )
            {
                console ( "Filter parameter: {$key} = {$value}" );

                // Also log decoded values
                $decodedValues = $this->getSelectedValues ( $value );
                console ( "Decoded values: " . json_encode ( $decodedValues, JSON_UNESCAPED_UNICODE ) );
            }
        }

        // Apply all filters
        $this->applyFilters ( $request, $query );

        // Log the query for debugging with better handling of boolean values
        $sql      = $query->toSql ();
        $bindings = $query->getBindings ();

        // Custom handling for boolean values in bindings
        foreach ( $bindings as $key => $binding )
        {
            if ( is_bool ( $binding ) )
            {
                // Convert boolean to string representation that PostgreSQL understands
                $value = $binding ? 'true' : 'false';
            }
            else
            {
                $value = is_numeric ( $binding ) ? $binding : "'" . addslashes ( $binding ) . "'";
            }
            $sql = preg_replace ( '/\?/', $value, $sql, 1 );
        }

        console ( "Final SPB Proyek Query: " . $sql );

        // Get paginated results
        $spbData = $query->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Now filter detail records for each SPB based on the criteria
        $filteredTableData = $this->filterDetailRecords ( $spbData, $filterParams );

        // Return the view with all necessary data
        return view ( 'dashboard.spb.proyek.detail.detail', [ 
            'headerPage'   => "SPB Proyek",
            'page'         => "Detail SPB Proyek [{$rkb->proyek->nama} | {$rkb->nomor}]",
            'TableData'    => $filteredTableData,
            'rkb'          => $rkb,
            'uniqueValues' => $this->prepareUniqueValues ( $uniqueValuesData ),
            'proyeks'      => $this->getFilteredProyeks (),
            'supplier'     => MasterDataSupplier::all (),
        ] );
    }

    /**
     * Filter detail records of each SPB based on the filter criteria
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $spbData
     * @param array $filterParams
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function filterDetailRecords ( $spbData, $filterParams )
    {
        if ( empty ( $filterParams ) )
        {
            console ( "No filter parameters, returning original data" );
            return $spbData; // No filtering needed
        }

        // Create a new collection where we'll store the filtered SPB data
        $spbDataCollection = collect ( $spbData->items () );

        console ( "Debug - Starting detail record filtering with " . count ( $spbDataCollection ) . " SPB items" );

        // Set up the filters we need to check within detail records
        $filterDetails = [ 
            'jenis_alat'  => [ 'field' => 'masterDataAlat.jenis_alat' ],
            'kode_alat'   => [ 'field' => 'masterDataAlat.kode_alat' ],
            'kategori'    => [ 'field' => 'masterDataSparepart.kategoriSparepart.nama' ],
            'sparepart'   => [ 'field' => 'masterDataSparepart.nama' ],
            'merk'        => [ 'field' => 'masterDataSparepart.merk' ],
            'quantity_po' => [ 'field' => 'quantity_po', 'numeric' => true ],
            'satuan'      => [ 'field' => 'satuan' ],
            'harga'       => [ 'field' => 'harga', 'numeric' => true ],
        ];

        // Special case filters
        $hasQuantityDiterimaFilter = isset ( $filterParams[ 'quantity_diterima' ] );
        $hasJumlahHargaFilter      = isset ( $filterParams[ 'jumlah_harga' ] );

        // For each SPB, filter its detail records
        foreach ( $spbDataCollection as $spbIndex => $spb )
        {
            if ( ! isset ( $spb->linkSpbDetailSpb ) )
            {
                console ( "Debug - SPB at index $spbIndex has no linkSpbDetailSpb" );
                continue;
            }

            // Original details from SPB
            $originalDetails = isset ( $spb->originalSpb ) ?
                collect ( $spb->originalSpb->linkSpbDetailSpb ) : collect ( $spb->linkSpbDetailSpb );

            console ( "Debug - SPB #" . $spb->id . " has " . count ( $originalDetails ) . " original details" );

            // Filter the detail records
            $filteredDetails = $originalDetails->filter ( function ($detailLink) use ($filterParams, $filterDetails, $hasQuantityDiterimaFilter, $hasJumlahHargaFilter)
            {
                $detail = $detailLink->detailSPB;
                if ( ! $detail ) return false;

                // Check each filter criteria
                foreach ( $filterParams as $param => $values )
                {
                    // Skip supplier filter as it's on the SPB level, not detail level
                    if ( $param === 'supplier' ) continue;

                    console ( "Debug - Checking filter for $param with values: " . json_encode ( $values ) );

                    // Handle special cases
                    if ( $param === 'quantity_diterima' )
                    {
                        $quantityDiterima = $detail->atbs->sum ( 'quantity' );
                        if ( ! $this->matchesNumericFilter ( $quantityDiterima, $values ) )
                        {
                            console ( "Debug - Failed quantity_diterima filter: $quantityDiterima doesn't match " . json_encode ( $values ) );
                            return false;
                        }
                        continue;
                    }

                    if ( $param === 'jumlah_harga' )
                    {
                        $jumlahHarga = $detail->harga * $detail->quantity_po;
                        if ( ! $this->matchesNumericFilter ( $jumlahHarga, $values ) )
                        {
                            console ( "Debug - Failed jumlah_harga filter: $jumlahHarga doesn't match " . json_encode ( $values ) );
                            return false;
                        }
                        continue;
                    }

                    // Regular field filters
                    if ( isset ( $filterDetails[ $param ] ) )
                    {
                        $field     = $filterDetails[ $param ][ 'field' ];
                        $isNumeric = $filterDetails[ $param ][ 'numeric' ] ?? false;
                        $parts = explode ( '.', $field );

                        // Navigate through nested properties
                        $value = $detail;
                        foreach ( $parts as $part )
                        {
                            if ( ! isset ( $value->$part ) )
                            {
                                $value = null;
                                break;
                            }
                            $value = $value->$part;
                        }

                        if ( $isNumeric )
                        {
                            // Handle numeric field with our dedicated helper
                            if ( ! $this->matchesNumericFilter ( (float) $value, $values ) )
                            {
                                console ( "Debug - Failed numeric filter for $param: $value doesn't match " . json_encode ( $values ) );
                                return false;
                            }
                        }
                        else
                        {
                            // Handle text field (original logic)
                            $hasEmptyFilter = in_array ( 'Empty/Null', $values );
                            $isEmpty      = ( $value === null || $value === '' || $value === '-' );
                            $matchesEmpty = ( $hasEmptyFilter && $isEmpty );
                            $matchesValue = in_array ( (string) $value, $values );

                            if ( ! $matchesEmpty && ! $matchesValue )
                            {
                                console ( "Debug - Failed text filter for $param: '$value' doesn't match " . json_encode ( $values ) );
                                return false;
                            }
                        }
                    }
                }

                // If we got here, all filter criteria matched
                return true;
            } )->values ()->all ();

            console ( "Debug - After filtering, SPB #" . $spb->id . " has " . count ( $filteredDetails ) . " details" );

            // For SPBs, replace the linkSpbDetailSpb with our filtered version
            if ( isset ( $spb->originalSpb ) )
            {
                $spb->originalSpb->linkSpbDetailSpb = $filteredDetails;
            }
            else
            {
                $spb->linkSpbDetailSpb = $filteredDetails;
            }
        }

        // Filter out SPBs that have no matching details after filtering
        $filteredSpbData = $spbDataCollection->filter ( function ($spb)
        {
            $details = isset ( $spb->originalSpb ) ?
                $spb->originalSpb->linkSpbDetailSpb : $spb->linkSpbDetailSpb;
            return count ( $details ) > 0;
        } )->values ();

        console ( "Debug - Final filtered SPB count: " . count ( $filteredSpbData ) );

        // Create a new paginator with our filtered data
        $filteredPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredSpbData,
            $spbData->total (), // We'll keep the original total for pagination
            $spbData->perPage (),
            $spbData->currentPage (),
            [ 'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath () ]
        );

        // Fix: Use request()->query() instead of $spbData->getQueryString()
        $filteredPaginator->appends ( request ()->query () );

        return $filteredPaginator;
    }

    /**
     * Helper to check if a numeric value matches the filter values
     * 
     * @param float $value The value to check
     * @param array $filterValues The filter values with possible prefixes
     * @return bool True if the value matches any filter condition
     */
    private function matchesNumericFilter ( $value, $filterValues )
    {
        console ( "Debug - matchesNumericFilter checking $value against " . json_encode ( $filterValues ) );

        // Check if the filter includes Empty/Null
        $hasEmptyFilter = in_array ( 'Empty/Null', $filterValues );

        // Empty check
        if ( $hasEmptyFilter && ( $value === null || $value === 0 || $value === '0' ) )
        {
            console ( "Debug - Value $value matched Empty/Null filter" );
            return true;
        }

        // Process gt:, lt:, exact: prefixes
        $gtValue     = null;
        $ltValue     = null;
        $exactValues = [];

        foreach ( $filterValues as $filterValue )
        {
            if ( $filterValue === 'Empty/Null' ) continue;

            if ( strpos ( $filterValue, 'gt:' ) === 0 )
            {
                $gtValue = (float) substr ( $filterValue, 3 );
            }
            elseif ( strpos ( $filterValue, 'lt:' ) === 0 )
            {
                $ltValue = (float) substr ( $filterValue, 3 );
            }
            elseif ( strpos ( $filterValue, 'exact:' ) === 0 )
            {
                $exactValues[] = (float) substr ( $filterValue, 6 );
            }
            elseif ( is_numeric ( $filterValue ) )
            {
                $exactValues[] = (float) $filterValue;
            }
        }

        // Range check (between gtValue and ltValue)
        if ( $gtValue !== null && $ltValue !== null )
        {
            if ( $value >= $gtValue && $value <= $ltValue )
            {
                console ( "Debug - Value $value matched range $gtValue-$ltValue" );
                return true;
            }
        }
        else
        {
            // Individual gt/lt checks
            if ( $gtValue !== null && $value >= $gtValue )
            {
                console ( "Debug - Value $value matched gt:$gtValue" );
                return true;
            }

            if ( $ltValue !== null && $value <= $ltValue )
            {
                console ( "Debug - Value $value matched lt:$ltValue" );
                return true;
            }
        }

        // Exact value check
        if ( ! empty ( $exactValues ) && in_array ( (float) $value, $exactValues ) )
        {
            console ( "Debug - Value $value matched exact values: " . json_encode ( $exactValues ) );
            return true;
        }

        console ( "Debug - Value $value didn't match any filters" );
        return false;
    }

    /**
     * Apply all filters from request to the query
     * 
     * @param Request $request HTTP request
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     */
    private function applyFilters ( Request $request, $query )
    {
        $filterMap = [ 
            'jenis_alat'        => [ 'table' => 'master_data_alat', 'column' => 'jenis_alat' ],
            'kode_alat'         => [ 'table' => 'master_data_alat', 'column' => 'kode_alat' ],
            'kategori'          => [ 'table' => 'kategori_sparepart', 'column' => 'nama' ],
            'sparepart'         => [ 'table' => 'master_data_sparepart', 'column' => 'nama' ],
            'merk'              => [ 'table' => 'master_data_sparepart', 'column' => 'merk' ],
            'supplier'          => [ 'table' => 'master_data_supplier', 'column' => 'nama' ],
            'quantity_po'       => [ 'table' => 'detail_spb', 'column' => 'quantity_po' ],
            'quantity_diterima' => [ 'special' => 'quantity_diterima' ], // Special case
            'satuan'            => [ 'table' => 'detail_spb', 'column' => 'satuan' ],
            'harga'             => [ 'table' => 'detail_spb', 'column' => 'harga' ],
            'jumlah_harga'      => [ 'special' => 'jumlah_harga' ], // Special calculated field
        ];

        foreach ( $filterMap as $param => $info )
        {
            $selectedValues = $this->getSelectedValues ( $request->input ( "selected_$param" ) );

            if ( ! empty ( $selectedValues ) )
            {
                console ( "DEBUG - Processing filter for '$param': " . json_encode ( $selectedValues ) );

                // Handle special cases
                if ( isset ( $info[ 'special' ] ) )
                {
                    if ( $info[ 'special' ] === 'quantity_diterima' )
                    {
                        $this->applyQuantityDiterimaFilter ( $query, $selectedValues );
                    }
                    elseif ( $info[ 'special' ] === 'jumlah_harga' )
                    {
                        $this->applyJumlahHargaFilter ( $query, $selectedValues );
                    }
                    continue;
                }

                // Handle supplier filter as a special case
                if ( $param === 'supplier' )
                {
                    $this->applySupplierFilter ( $query, $selectedValues );
                    continue;
                }

                // Apply standard filter
                $this->applyDetailFilter ( $query, $info[ 'table' ], $info[ 'column' ], $selectedValues );
            }
        }

        // After all filters are applied, log the final query
        console ( "DEBUG - FINAL FULL QUERY: " . $this->getFullSqlWithBindings ( $query ) );
    }

    /**
     * Apply quantity_diterima filter (special case that uses atbs relationship)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     * @param array $values Selected filter values
     */
    private function applyQuantityDiterimaFilter ( $query, $values )
    {
        console ( "DEBUG - applyQuantityDiterimaFilter" );
        console ( "DEBUG - Initial filter values: " . json_encode ( $values ) );

        // Parse all the filter values
        $hasEmptyFilter = in_array ( 'Empty/Null', $values );
        $gtValue        = null;
        $ltValue        = null;
        $exactValues    = [];

        foreach ( $values as $value )
        {
            console ( "DEBUG - Processing quantity filter: '$value'" );

            if ( strpos ( $value, 'gt:' ) === 0 )
            {
                $gtValue = (float) substr ( $value, 3 );
                console ( "DEBUG - Found quantity GT: $gtValue" );
            }
            elseif ( strpos ( $value, 'lt:' ) === 0 )
            {
                $ltValue = (float) substr ( $value, 3 );
                console ( "DEBUG - Found quantity LT: $ltValue" );
            }
            elseif ( strpos ( $value, 'exact:' ) === 0 )
            {
                $exactValue    = (float) substr ( $value, 6 );
                $exactValues[] = $exactValue;
                console ( "DEBUG - Found quantity EXACT: $exactValue" );
            }
            elseif ( $value !== 'Empty/Null' && is_numeric ( $value ) )
            {
                $exactValues[] = (float) $value;
                console ( "DEBUG - Found quantity numeric: $value" );
            }
        }

        // Use raw SQL for better PostgreSQL compatibility
        $query->whereIn ( 'id', function ($mainSubquery) use ($hasEmptyFilter, $gtValue, $ltValue, $exactValues)
        {
            $mainSubquery->select ( 'link_spb_detail_spb.id_spb' )
                ->from ( 'link_spb_detail_spb' )
                ->join ( 'detail_spb', 'link_spb_detail_spb.id_detail_spb', '=', 'detail_spb.id' );

            // Build conditions and bindings
            $conditions = [];
            $bindings   = [];

            if ( $hasEmptyFilter )
            {
                $conditions[] = "(NOT EXISTS (SELECT 1 FROM atb WHERE atb.id_detail_spb = detail_spb.id) OR (SELECT COALESCE(SUM(quantity), 0) FROM atb WHERE atb.id_detail_spb = detail_spb.id) = 0)";
                console ( "DEBUG - Added quantity Empty/Null condition" );
            }

            if ( $gtValue !== null && $ltValue !== null )
            {
                $conditions[] = "((SELECT COALESCE(SUM(quantity), 0) FROM atb WHERE atb.id_detail_spb = detail_spb.id) BETWEEN ? AND ?)";
                $bindings[]   = $gtValue;
                $bindings[]   = $ltValue;
                console ( "DEBUG - Added quantity BETWEEN: $gtValue AND $ltValue" );
            }
            else
            {
                if ( $gtValue !== null )
                {
                    $conditions[] = "((SELECT COALESCE(SUM(quantity), 0) FROM atb WHERE atb.id_detail_spb = detail_spb.id) >= ?)";
                    $bindings[]   = $gtValue;
                    console ( "DEBUG - Added quantity GTE: $gtValue" );
                }
                if ( $ltValue !== null )
                {
                    $conditions[] = "((SELECT COALESCE(SUM(quantity), 0) FROM atb WHERE atb.id_detail_spb = detail_spb.id) <= ?)";
                    $bindings[]   = $ltValue;
                    console ( "DEBUG - Added quantity LTE: $ltValue" );
                }
            }

            if ( ! empty ( $exactValues ) )
            {
                $placeholders = implode ( ',', array_fill ( 0, count ( $exactValues ), '?' ) );
                $conditions[] = "((SELECT COALESCE(SUM(quantity), 0) FROM atb WHERE atb.id_detail_spb = detail_spb.id) IN ($placeholders))";
                foreach ( $exactValues as $value )
                {
                    $bindings[] = $value;
                }
                console ( "DEBUG - Added quantity IN condition with values: " . json_encode ( $exactValues ) );
            }

            // Combine all conditions with OR
            if ( ! empty ( $conditions ) )
            {
                $sql = '(' . implode ( ' OR ', $conditions ) . ')';
                console ( "DEBUG - Quantity filter SQL: $sql" );
                console ( "DEBUG - Quantity filter bindings: " . json_encode ( $bindings ) );
                $mainSubquery->whereRaw ( $sql, $bindings );

                // Get full SQL for debugging
                $fullSql = $this->getFullSqlWithBindings ( $mainSubquery );
                console ( "DEBUG - Full quantity filter subquery SQL: $fullSql" );
            }
            else
            {
                console ( "DEBUG - No quantity conditions generated" );
            }
        } );
    }

    /**
     * Apply standard filter for detail_spb fields and related tables
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     * @param string $table Table name
     * @param string $column Column name
     * @param array $values Selected filter values
     */
    private function applyDetailFilter ( $query, $table, $column, $values )
    {
        $hasEmptyFilter = in_array ( 'Empty/Null', $values );
        $nonEmptyValues = array_filter ( $values, fn ( $v ) => $v !== 'Empty/Null' );

        // Log what we're filtering for debugging
        console ( "Applying filter for $table.$column with values: " . json_encode ( $values ) );
        console ( "Has Empty/Null filter: " . ( $hasEmptyFilter ? 'true' : 'false' ) );
        console ( "Non-empty values: " . json_encode ( $nonEmptyValues ) );

        // Check if this is a numeric column
        $isNumericColumn = in_array ( $column, [ 'quantity_po', 'harga' ] );

        // This is a significant change: using whereIn instead of whereExists for better reliability
        $query->whereIn ( 'id', function ($mainSubquery) use ($table, $column, $values, $isNumericColumn)
        {
            $mainSubquery->select ( 'link_spb_detail_spb.id_spb' )
                ->from ( 'link_spb_detail_spb' )
                ->join ( 'detail_spb', 'link_spb_detail_spb.id_detail_spb', '=', 'detail_spb.id' );

            // Add appropriate join based on table
            if ( $table !== 'detail_spb' )
            {
                if ( $table === 'kategori_sparepart' )
                {
                    $mainSubquery->join ( 'master_data_sparepart', 'detail_spb.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
                        ->join ( $table, 'master_data_sparepart.id_kategori_sparepart', '=', "$table.id" );
                }
                else
                {
                    $mainSubquery->join ( $table, "detail_spb.id_$table", '=', "$table.id" );
                }
            }

            // Define the column for filtering
            $fullColumn = $table === 'detail_spb' ? "$column" : "$table.$column";

            if ( $isNumericColumn )
            {
                // Use direct PostgreSQL compatible conditions for numeric columns
                $this->applyNumericFilterPostgres ( $mainSubquery, $fullColumn, $values );
            }
            else
            {
                // Handle text columns - existing code
                $hasEmptyFilter = in_array ( 'Empty/Null', $values );
                $nonEmptyValues = array_filter ( $values, fn ( $v ) => $v !== 'Empty/Null' );

                if ( $hasEmptyFilter )
                {
                    $mainSubquery->where ( function ($q) use ($fullColumn, $nonEmptyValues)
                    {
                        $q->whereNull ( $fullColumn )
                            ->orWhere ( $fullColumn, '' )
                            ->orWhere ( $fullColumn, '-' );

                        if ( ! empty ( $nonEmptyValues ) )
                        {
                            foreach ( $nonEmptyValues as $value )
                            {
                                $q->orWhereRaw ( "LOWER($fullColumn) = LOWER(?)", [ $value ] );
                            }
                        }
                    } );
                }
                else
                {
                    $mainSubquery->where ( function ($q) use ($fullColumn, $nonEmptyValues)
                    {
                        foreach ( $nonEmptyValues as $value )
                        {
                            $q->orWhereRaw ( "LOWER($fullColumn) = LOWER(?)", [ $value ] );
                        }
                    } );
                }
            }
        } );
    }

    /**
     * Apply numeric filter using PostgreSQL specific SQL
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $column Full column name (with table)
     * @param array $values Filter values
     */
    private function applyNumericFilterPostgres ( $query, $column, $values )
    {
        console ( "DEBUG - applyNumericFilterPostgres for column: $column" );
        console ( "DEBUG - Filter values: " . json_encode ( $values ) );

        $hasEmptyFilter = in_array ( 'Empty/Null', $values );
        $conditions     = [];
        $bindings       = [];

        // Parse all the filter values
        $gtValue     = null;
        $ltValue     = null;
        $exactValues = [];

        // Detailed logging of input values
        foreach ( $values as $value )
        {
            console ( "DEBUG - Processing filter value: '$value'" );

            if ( strpos ( $value, 'gt:' ) === 0 )
            {
                $gtValue = (float) substr ( $value, 3 );
                console ( "DEBUG - Found GT filter: $gtValue" );
            }
            elseif ( strpos ( $value, 'lt:' ) === 0 )
            {
                $ltValue = (float) substr ( $value, 3 );
                console ( "DEBUG - Found LT filter: $ltValue" );
            }
            elseif ( strpos ( $value, 'exact:' ) === 0 )
            {
                $exactValue    = (float) substr ( $value, 6 );
                $exactValues[] = $exactValue;
                console ( "DEBUG - Found EXACT filter: $exactValue" );
            }
            elseif ( $value !== 'Empty/Null' && is_numeric ( $value ) )
            {
                $exactValues[] = (float) $value;
                console ( "DEBUG - Found numeric value: $value" );
            }
        }

        // Log what was extracted
        console ( "DEBUG - After parsing numeric filters:" );
        console ( "DEBUG - hasEmptyFilter: " . ( $hasEmptyFilter ? 'true' : 'false' ) );
        console ( "DEBUG - gtValue: " . ( $gtValue !== null ? $gtValue : 'null' ) );
        console ( "DEBUG - ltValue: " . ( $ltValue !== null ? $ltValue : 'null' ) );
        console ( "DEBUG - exactValues: " . json_encode ( $exactValues ) );

        // Build SQL conditions
        if ( $hasEmptyFilter )
        {
            $conditions[] = "($column IS NULL OR $column = 0)";
            console ( "DEBUG - Added NULL condition: ($column IS NULL OR $column = 0)" );
        }

        if ( $gtValue !== null && $ltValue !== null )
        {
            $conditions[] = "($column::numeric BETWEEN ?::numeric AND ?::numeric)";
            $bindings[]   = $gtValue;
            $bindings[]   = $ltValue;
            console ( "DEBUG - Added BETWEEN condition: ($column::numeric BETWEEN $gtValue::numeric AND $ltValue::numeric)" );
        }
        else
        {
            if ( $gtValue !== null )
            {
                $conditions[] = "($column::numeric >= ?::numeric)";
                $bindings[]   = $gtValue;
                console ( "DEBUG - Added GT condition: ($column::numeric >= $gtValue::numeric)" );
            }
            if ( $ltValue !== null )
            {
                $conditions[] = "($column::numeric <= ?::numeric)";
                $bindings[]   = $ltValue;
                console ( "DEBUG - Added LT condition: ($column::numeric <= $ltValue::numeric)" );
            }
        }

        if ( ! empty ( $exactValues ) )
        {
            $placeholders = implode ( ',', array_fill ( 0, count ( $exactValues ), '?::numeric' ) );
            $conditions[] = "($column::numeric IN ($placeholders))";
            foreach ( $exactValues as $value )
            {
                $bindings[] = $value;
            }
            console ( "DEBUG - Added IN condition with values: " . json_encode ( $exactValues ) );
        }

        // Combine all conditions with OR
        if ( ! empty ( $conditions ) )
        {
            $sql = '(' . implode ( ' OR ', $conditions ) . ')';
            console ( "DEBUG - Final SQL condition: $sql" );
            console ( "DEBUG - SQL bindings: " . json_encode ( $bindings ) );

            // Test the query without actually running it
            $testQuery = clone $query;
            $testQuery->whereRaw ( $sql, $bindings );
            $compiledSql = $this->getFullSqlWithBindings ( $testQuery );
            console ( "DEBUG - Test compiled SQL: $compiledSql" );

            // Apply the actual filter
            $query->whereRaw ( $sql, $bindings );
        }
        else
        {
            console ( "DEBUG - No conditions were generated for numeric filter" );
        }
    }

    /**
     * Apply jumlah_harga filter (calculated field)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     * @param array $values Selected filter values
     */
    private function applyJumlahHargaFilter ( $query, $values )
    {
        console ( "DEBUG - applyJumlahHargaFilter" );
        console ( "DEBUG - Initial filter values: " . json_encode ( $values ) );

        // Parse all the filter values
        $hasEmptyFilter = in_array ( 'Empty/Null', $values );
        $gtValue        = null;
        $ltValue        = null;
        $exactValues    = [];

        foreach ( $values as $value )
        {
            console ( "DEBUG - Processing jumlah_harga filter: '$value'" );

            if ( strpos ( $value, 'gt:' ) === 0 )
            {
                $gtValue = (float) substr ( $value, 3 );
                console ( "DEBUG - Found jumlah_harga GT: $gtValue" );
            }
            elseif ( strpos ( $value, 'lt:' ) === 0 )
            {
                $ltValue = (float) substr ( $value, 3 );
                console ( "DEBUG - Found jumlah_harga LT: $ltValue" );
            }
            elseif ( strpos ( $value, 'exact:' ) === 0 )
            {
                $exactValue    = (float) substr ( $value, 6 );
                $exactValues[] = $exactValue;
                console ( "DEBUG - Found jumlah_harga EXACT: $exactValue" );
            }
            elseif ( $value !== 'Empty/Null' && is_numeric ( $value ) )
            {
                $exactValues[] = (float) $value;
                console ( "DEBUG - Found jumlah_harga numeric: $value" );
            }
        }

        // Use raw SQL for better PostgreSQL compatibility
        $query->whereIn ( 'id', function ($mainSubquery) use ($hasEmptyFilter, $gtValue, $ltValue, $exactValues)
        {
            $mainSubquery->select ( 'link_spb_detail_spb.id_spb' )
                ->from ( 'link_spb_detail_spb' )
                ->join ( 'detail_spb', 'link_spb_detail_spb.id_detail_spb', '=', 'detail_spb.id' );

            // Build conditions and bindings
            $conditions = [];
            $bindings   = [];

            if ( $hasEmptyFilter )
            {
                $conditions[] = "(detail_spb.harga IS NULL OR detail_spb.harga = 0 OR detail_spb.quantity_po IS NULL OR detail_spb.quantity_po = 0)";
                console ( "DEBUG - Added jumlah_harga Empty/Null condition" );
            }

            if ( $gtValue !== null && $ltValue !== null )
            {
                $conditions[] = "((detail_spb.harga * detail_spb.quantity_po)::numeric BETWEEN ?::numeric AND ?::numeric)";
                $bindings[]   = $gtValue;
                $bindings[]   = $ltValue;
                console ( "DEBUG - Added jumlah_harga BETWEEN: $gtValue AND $ltValue" );
            }
            else
            {
                if ( $gtValue !== null )
                {
                    $conditions[] = "((detail_spb.harga * detail_spb.quantity_po)::numeric >= ?::numeric)";
                    $bindings[]   = $gtValue;
                    console ( "DEBUG - Added jumlah_harga GTE: $gtValue" );
                }
                if ( $ltValue !== null )
                {
                    $conditions[] = "((detail_spb.harga * detail_spb.quantity_po)::numeric <= ?::numeric)";
                    $bindings[]   = $ltValue;
                    console ( "DEBUG - Added jumlah_harga LTE: $ltValue" );
                }
            }

            if ( ! empty ( $exactValues ) )
            {
                $placeholders = implode ( ',', array_fill ( 0, count ( $exactValues ), '?::numeric' ) );
                $conditions[] = "((detail_spb.harga * detail_spb.quantity_po)::numeric IN ($placeholders))";
                foreach ( $exactValues as $value )
                {
                    $bindings[] = $value;
                }
                console ( "DEBUG - Added jumlah_harga IN condition with values: " . json_encode ( $exactValues ) );
            }

            // Combine all conditions with OR
            if ( ! empty ( $conditions ) )
            {
                $sql = '(' . implode ( ' OR ', $conditions ) . ')';
                console ( "DEBUG - Jumlah harga SQL: $sql" );
                console ( "DEBUG - Jumlah harga bindings: " . json_encode ( $bindings ) );
                $mainSubquery->whereRaw ( $sql, $bindings );

                // Get full SQL for debugging
                $fullSql = $this->getFullSqlWithBindings ( $mainSubquery );
                console ( "DEBUG - Full jumlah_harga subquery SQL: $fullSql" );
            }
            else
            {
                console ( "DEBUG - No jumlah_harga conditions generated" );
            }
        } );
    }

    /**
     * Helper function to get the full SQL with bindings for debugging
     *
     * @param mixed $query The query builder
     * @return string The SQL with bindings replaced
     */
    private function getFullSqlWithBindings ( $query )
    {
        $sql      = $query->toSql ();
        $bindings = $query->getBindings ();

        // Replace each binding
        foreach ( $bindings as $binding )
        {
            if ( is_numeric ( $binding ) )
            {
                $value = $binding;
            }
            elseif ( is_bool ( $binding ) )
            {
                $value = $binding ? 'true' : 'false';
            }
            else
            {
                $value = "'" . addslashes ( $binding ) . "'";
            }
            $sql = preg_replace ( '/\?/', $value, $sql, 1 );
        }

        return $sql;
    }

    /**
     * Prepare unique values for the view
     * 
     * @param array $uniqueValuesData Raw unique values
     * @return array Structured unique values
     */
    private function prepareUniqueValues ( $uniqueValuesData )
    {
        return [ 
            'jenis_alat'        => $uniqueValuesData[ 'uniqueJenisAlat' ],
            'kode_alat'         => $uniqueValuesData[ 'uniqueKodeAlat' ],
            'kategori'          => $uniqueValuesData[ 'uniqueKategori' ],
            'sparepart'         => $uniqueValuesData[ 'uniqueSparepart' ],
            'merk'              => $uniqueValuesData[ 'uniqueMerk' ],
            'supplier'          => $uniqueValuesData[ 'uniqueSupplier' ],
            'quantity_po'       => $uniqueValuesData[ 'uniqueQuantityPO' ],
            'quantity_diterima' => $uniqueValuesData[ 'uniqueQuantityDiterima' ],
            'satuan'            => $uniqueValuesData[ 'uniqueSatuan' ],
            'harga'             => $uniqueValuesData[ 'uniqueHarga' ],
            'jumlah_harga'      => $uniqueValuesData[ 'uniqueJumlahHarga' ],
        ];
    }

    /**
     * Get filtered projects based on user role
     * 
     * @return \Illuminate\Database\Eloquent\Collection Projects collection
     */
    private function getFilteredProyeks ()
    {
        $user  = Auth::user ();
        $query = Proyek::with ( "users" );

        if ( $user->role === 'koordinator_proyek' )
        {
            $query->whereHas ( 'users', function ($q) use ($user)
            {
                $q->where ( 'users.id', $user->id );
            } );
        }

        return $query->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();
    }

    /**
     * Apply supplier filter to the query
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder
     * @param array $values Selected filter values
     */
    private function applySupplierFilter ( $query, $values )
    {
        console ( "DEBUG - applySupplierFilter with values: " . json_encode ( $values ) );

        $hasEmptyFilter = in_array ( 'Empty/Null', $values );
        $nonEmptyValues = array_filter ( $values, fn ( $v ) => $v !== 'Empty/Null' );

        if ( $hasEmptyFilter && empty ( $nonEmptyValues ) )
        {
            // Only Empty/Null filter
            $query->where ( function ($q)
            {
                $q->whereNull ( 'id_master_data_supplier' )
                    ->orWhereRaw ( 'id_master_data_supplier = 0' );
            } );
            console ( "DEBUG - Applied Empty/Null supplier filter only" );
        }
        elseif ( $hasEmptyFilter )
        {
            // Empty/Null plus specific values
            $query->where ( function ($q) use ($nonEmptyValues)
            {
                $q->whereNull ( 'id_master_data_supplier' )
                    ->orWhereRaw ( 'id_master_data_supplier = 0' )
                    ->orWhereIn ( 'id_master_data_supplier', function ($subquery) use ($nonEmptyValues)
                    {
                        $subquery->select ( 'id' )
                            ->from ( 'master_data_supplier' )
                            ->whereIn ( 'nama', $nonEmptyValues );
                    } );
            } );
            console ( "DEBUG - Applied Empty/Null + specific supplier filter: " . json_encode ( $nonEmptyValues ) );
        }
        else
        {
            // Only specific values
            $query->whereIn ( 'id_master_data_supplier', function ($subquery) use ($nonEmptyValues)
            {
                $subquery->select ( 'id' )
                    ->from ( 'master_data_supplier' )
                    ->whereIn ( 'nama', $nonEmptyValues );
            } );
            console ( "DEBUG - Applied specific supplier filter: " . json_encode ( $nonEmptyValues ) );
        }

        // Log the resulting SQL for debugging
        $fullSql = $this->getFullSqlWithBindings ( $query );
        console ( "DEBUG - Supplier filter final SQL: $fullSql" );
    }
}
