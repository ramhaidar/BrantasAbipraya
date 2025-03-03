<?php

namespace App\Http\Controllers;

use App\Models\APB;
use App\Models\ATB;
use App\Models\Alat;
use App\Models\User;
use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\KategoriSparepart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Define categories as a class constant to avoid recreating it multiple times
    private const CATEGORIES = [ 
        [ "kode" => "A1", "nama" => "CABIN", "jenis" => "Perbaikan" ],
        [ "kode" => "A2", "nama" => "ENGINE SYSTEM", "jenis" => "Perbaikan" ],
        [ 
            "kode"  => "A3",
            "nama"  => "TRANSMISSION SYSTEM",
            "jenis" => "Perbaikan",
        ],
        [ 
            "kode"  => "A4",
            "nama"  => "CHASSIS & SWING MACHINERY",
            "jenis" => "Perbaikan",
        ],
        [ 
            "kode"  => "A5",
            "nama"  => "DIFFERENTIAL SYSTEM",
            "jenis" => "Perbaikan",
        ],
        [ "kode" => "A6", "nama" => "ELECTRICAL SYSTEM", "jenis" => "Perbaikan" ],
        [ 
            "kode"  => "A7",
            "nama"  => "HYDRAULIC/PNEUMATIC SYSTEM",
            "jenis" => "Perbaikan",
        ],
        [ "kode" => "A8", "nama" => "STEERING SYSTEM", "jenis" => "Perbaikan" ],
        [ "kode" => "A9", "nama" => "BRAKE SYSTEM", "jenis" => "Perbaikan" ],
        [ "kode" => "A10", "nama" => "SUSPENSION", "jenis" => "Perbaikan" ],
        [ "kode" => "A11", "nama" => "WORK EQUIPMENT", "jenis" => "Perbaikan" ],
        [ "kode" => "A12", "nama" => "UNDERCARRIAGE", "jenis" => "Perbaikan" ],
        [ "kode" => "A13", "nama" => "FINAL DRIVE", "jenis" => "Perbaikan" ],
        [ "kode" => "A14", "nama" => "FREIGHT COST", "jenis" => "Perbaikan" ],
        [ 
            "kode"     => "B11",
            "nama"     => "Oil Filter",
            "jenis"    => "Pemeliharaan",
            "subJenis" => "MAINTENANCE KIT",
        ],
        [ 
            "kode"     => "B12",
            "nama"     => "Fuel Filter",
            "jenis"    => "Pemeliharaan",
            "subJenis" => "MAINTENANCE KIT",
        ],
        [ 
            "kode"     => "B13",
            "nama"     => "Air Filter",
            "jenis"    => "Pemeliharaan",
            "subJenis" => "MAINTENANCE KIT",
        ],
        [ 
            "kode"     => "B21",
            "nama"     => "Engine Oil",
            "jenis"    => "Pemeliharaan",
            "subJenis" => "OIL & LUBRICANTS",
        ],
        [ 
            "kode"     => "B22",
            "nama"     => "Hydraulic Oil",
            "jenis"    => "Pemeliharaan",
            "subJenis" => "OIL & LUBRICANTS",
        ],
        [ "kode" => "B3", "nama" => "TYRE", "jenis" => "Pemeliharaan" ],
        [ "kode" => "C1", "nama" => "WORKSHOP", "jenis" => "Material" ],
    ];

    private const VALID_TYPES = [ 
        "hutang-unit-alat",
        "panjar-unit-alat",
        "mutasi-proyek",
        "panjar-proyek",
    ];

    public function index ( Request $request )
    {
        $id_proyek = $request->query ( "id_proyek" );

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

        // Track whether date parameters were provided
        $hasDateParams = $request->has ( 'startDate' ) && ! empty ( $request->startDate ) &&
            $request->has ( 'endDate' ) && ! empty ( $request->endDate );

        // Date ranges - Parse from request parameters or use defaults
        $currentDate = now ();

        // Check if startDate is provided in the request
        if ( $request->has ( 'startDate' ) && ! empty ( $request->startDate ) )
        {
            try
            {
                // Parse YYYY-MM format from input
                // Set to the 26th day of the selected month
                $startDate = Carbon::createFromFormat ( 'Y-m', $request->startDate )->setDay ( 26 );
            }
            catch ( \Exception $e )
            {
                // Fallback to 26th of current month if invalid
                $startDate = $currentDate->copy ()->setDay ( 26 );
                // If current day is before 26th, use previous month
                if ( $currentDate->day < 26 )
                {
                    $startDate->subMonth ();
                }
            }
        }
        else
        {
            // Default: 26th of current month or previous month
            $startDate = $currentDate->copy ()->setDay ( 26 );
            // If current day is before 26th, use previous month
            if ( $currentDate->day < 26 )
            {
                $startDate->subMonth ();
            }
        }

        // Check if endDate is provided in the request
        if ( $request->has ( 'endDate' ) && ! empty ( $request->endDate ) )
        {
            try
            {
                // Parse the YYYY-MM format from input and set to the 25th day
                // Removed the addMonth() call that was causing incorrect date
                $endDate = Carbon::createFromFormat ( 'Y-m', $request->endDate )
                    ->setDay ( 25 ); // Set to day 25 of the specified month
            }
            catch ( \Exception $e )
            {
                // Fallback if invalid: 25th of next month from startDate
                $endDate = $startDate->copy ()->addMonth ()->setDay ( 25 );
            }
        }
        else
        {
            // Default: 25th of the month after startDate
            $endDate = $startDate->copy ()->addMonth ()->setDay ( 25 );
        }

        // Ensure endDate is not before startDate (this should rarely happen with our logic)
        if ( $endDate->lt ( $startDate ) )
        {
            $endDate = $startDate->copy ()->addMonth ()->setDay ( 25 );
        }

        // Build base queries
        [ $atbQuery, $apbQuery, $saldoQuery ] = $this->buildBaseQueries (
            $user,
            $id_proyek
        );

        // Get data for different date ranges using the parsed dates
        $data = $this->getQueriesData (
            $atbQuery,
            $apbQuery,
            $saldoQuery,
            $startDate,
            $endDate
        );

        // Create filtered data specifically for the main totals
        $filteredData = $this->getFilteredDataForTotals (
            $atbQuery,
            $apbQuery,
            $saldoQuery,
            $startDate,
            $endDate,
            $hasDateParams
        );

        // dd ( $data[ 'atbDataTotal' ] );

        // Calculate chart data
        $chartData = $this->calculateAllChartData ( $data );

        // Calculate horizontal charts
        $horizontalCharts = $this->calculateHorizontalCharts ( $proyeks, $data );

        // Add these lines before the return statement
        $categoryData = [];
        if ( $id_proyek )
        {
            $categoryData = $this->getCategoryDataForProject (
                $id_proyek,
                $data[ 'atbDataCurrent' ],
                $data[ 'apbDataCurrent' ],
                $data[ 'saldoDataCurrent' ],
                $data[ 'atbDataTotal' ],
                $data[ 'apbDataTotal' ],
                $data[ 'saldoDataTotal' ]
            );
        }

        // dd ( $categoryData );

        console ( "START DATE: " . $startDate->format ( 'Y-m-d' ) );
        console ( "END DATE: " . $endDate->format ( 'Y-m-d' ) );

        console ( "ATB TOTAL COUNT: " . $data[ 'atbDataTotal' ]->count () );
        console ( "FILTERED ATB COUNT: " . $filteredData[ 'atbData' ]->count () );

        // Sample a few records
        if ( $data[ 'atbDataTotal' ]->isNotEmpty () )
        {
            console ( "FIRST ATB RECORD DATE: " . $data[ 'atbDataTotal' ]->first ()->tanggal );
            console ( "FIRST ATB RECORD TIPE: " . $data[ 'atbDataTotal' ]->first ()->tipe );
        }


        // Calculate totals using the correct ATB-APB formula for saldo
        $totalATB   = $this->calculateATBTotal ( $filteredData[ "atbData" ] );
        $totalAPB   = $this->calculateAPBTotal ( $filteredData[ "apbData" ] );
        $totalSaldo = $totalATB - $totalAPB; // Calculate saldo as ATB - APB

        // Calculate monthly financial data for ATB, APB, and Saldo
        $monthlyFinancialData = $this->calculateMonthlyFinancialData ( $id_proyek );

        return view ( "dashboard.dashboard.dashboard", [ 
            "headerPage"                 => "Dashboard",
            "page"                       => "Dashboard",

            "proyeks"                    => $proyeks,
            "selectedProject"            => $id_proyek,

            "test"                       => $data[ "atbDataTotal" ],
            // Use the calculated values instead of calling calculateOverallTotal
            "totalATB"                   => $totalATB,
            "totalAPB"                   => $totalAPB,
            "totalSaldo"                 => $totalSaldo,

            "chartData"                  => $chartData[ "main" ],
            "chartDataCurrent"           => $chartData[ "current" ],
            "chartDataTotal"             => $chartData[ "total" ],

            "startDate"                  => $startDate->format ( "Y-m-d" ),
            "endDate"                    => $endDate->format ( "Y-m-d" ),

            "horizontalChartCurrent"     => $horizontalCharts[ "current" ],
            "horizontalChartTotal"       => $horizontalCharts[ "total" ],
            "categoryData"               => $categoryData,
            "formatSaldoWithParentheses" => true,

            // Add monthly financial data for the charts
            "monthlyFinancialData"       => $monthlyFinancialData,
        ] );
    }

    // New method to get filtered data specifically for the totals
    private function getFilteredDataForTotals ( $atbQuery, $apbQuery, $saldoQuery, $startDate, $endDate, $applyDateFilter = true )
    {
        // Clone the queries to prevent modifying the originals
        $atbBase = clone $atbQuery->with ( [ 'masterDataSparepart.kategoriSparepart', 'saldo' ] )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' );

        $apbBase = clone $apbQuery->with ( [ 'masterDataSparepart.kategoriSparepart', 'saldo' ] )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' );

        $saldoBase = clone $saldoQuery->with ( [ 'masterDataSparepart.kategoriSparepart', 'atb' ] )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' );

        // Apply date filtering conditionally
        if ( $applyDateFilter )
        {
            return [ 
                "atbData"   => ( clone $atbBase )
                    ->whereBetween ( "tanggal", [ $startDate, $endDate ] )
                    ->get (),
                "apbData"   => ( clone $apbBase )
                    ->whereBetween ( "tanggal", [ $startDate, $endDate ] )
                    ->get (),
                "saldoData" => ( clone $saldoBase )
                    ->whereHas (
                        "atb",
                        fn ( $q ) => $q->whereBetween ( "tanggal", [ $startDate, $endDate ] )
                    )
                    ->get (),
            ];
        }
        else
        {
            // No date filtering - get all data that matches the project and type
            return [ 
                "atbData"   => ( clone $atbBase )->get (),
                "apbData"   => ( clone $apbBase )->get (),
                "saldoData" => ( clone $saldoBase )->get (),
            ];
        }
    }

    private function buildBaseQueries ( $user, $id_proyek )
    {
        $atbQuery   = ATB::query ();
        $apbQuery   = APB::with ( "saldo" );
        $saldoQuery = Saldo::query ();

        if ( $id_proyek )
        {
            $this->applyProjectFilter (
                [ $atbQuery, $apbQuery, $saldoQuery ],
                $id_proyek
            );
        }

        return [ $atbQuery, $apbQuery, $saldoQuery ];
    }

    private function applyProjectFilter ( array $queries, $id_proyek )
    {
        $queries[ 0 ]->where ( "id_proyek", $id_proyek );
        $queries[ 1 ]->where ( "id_proyek", $id_proyek );
        $queries[ 2 ]->whereHas (
            "atb",
            fn ( $q ) => $q->where ( "id_proyek", $id_proyek )
        );
    }

    private function applyUserProjectsFilter ( array $queries, $projectIds )
    {
        $queries[ 0 ]->whereIn ( "id_proyek", $projectIds );
        $queries[ 1 ]->whereIn ( "id_proyek", $projectIds );
        $queries[ 2 ]->whereHas (
            "atb",
            fn ( $q ) => $q->whereIn ( "id_proyek", $projectIds )
        );
    }

    private function getQueriesData ( $atbQuery, $apbQuery, $saldoQuery, $startDate, $endDate )
    {
        // First, get base queries with proper eager loading
        $atbBase = clone $atbQuery->with ( [ 'masterDataSparepart.kategoriSparepart', 'saldo' ] )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' );

        $apbBase = clone $apbQuery->with ( [ 'masterDataSparepart.kategoriSparepart', 'saldo' ] )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' );

        $saldoBase = clone $saldoQuery->with ( [ 'masterDataSparepart.kategoriSparepart', 'atb' ] )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' );

        // Calculate the start of month for current period
        $currentPeriodStart = $startDate->copy ();

        // Calculate the end of month for current period
        $currentPeriodEnd = $endDate->copy ();

        console ( "PERIOD START: " . $currentPeriodStart->format ( 'Y-m-d' ) );
        console ( "PERIOD END: " . $currentPeriodEnd->format ( 'Y-m-d' ) );

        // dd ( ( clone $saldoBase )
        //     ->whereHas (
        //         "atb",
        //         fn ( $q ) =>
        //         $q->whereBetween ( "tanggal", [ $currentPeriodStart, $currentPeriodEnd ] )
        //     )
        //     ->get () );

        // Build all queries using the base queries
        return [ 
            "atbData"          => ( clone $atbBase )->get (),
            "apbData"          => ( clone $apbBase )->get (),
            "saldoData"        => ( clone $saldoBase )->get (),

            "atbDataCurrent"   => ( clone $atbBase )
                ->whereBetween ( "tanggal", [ $currentPeriodStart, $currentPeriodEnd ] )
                ->get (),
            "apbDataCurrent"   => ( clone $apbBase )
                ->whereBetween ( "tanggal", [ $currentPeriodStart, $currentPeriodEnd ] )
                ->get (),
            "saldoDataCurrent" => ( clone $saldoBase )
                ->whereHas (
                    "atb",
                    fn ( $q ) =>
                    $q->whereBetween ( "tanggal", [ $currentPeriodStart, $currentPeriodEnd ] )
                )
                ->get (),

            // Keep the original logic for the charts - data up to the end date
            "atbDataTotal"     => ( clone $atbBase )
                ->where ( "tanggal", "<=", $currentPeriodEnd )
                ->get (),
            "apbDataTotal"     => ( clone $apbBase )
                ->where ( "tanggal", "<=", $currentPeriodEnd )
                ->get (),
            "saldoDataTotal"   => ( clone $saldoBase )
                ->whereHas (
                    "atb",
                    fn ( $q ) =>
                    $q->where ( "tanggal", "<=", $currentPeriodEnd )
                )
                ->get (),
        ];
    }

    private function calculateAllChartData ( $data )
    {
        return [ 
            "main"    => $this->calculateChartData (
                $data[ "atbData" ],
                $data[ "apbData" ],
                $data[ "saldoData" ]
            ),
            "current" => $this->calculateChartData (
                $data[ "atbDataCurrent" ],
                $data[ "apbDataCurrent" ],
                $data[ "saldoDataCurrent" ]
            ),
            "total"   => $this->calculateChartData (
                $data[ "atbDataTotal" ],
                $data[ "apbDataTotal" ],
                $data[ "saldoDataTotal" ]
            ),
        ];
    }

    private function calculateHorizontalCharts ( $proyeks, $data )
    {
        $charts = [ "current" => [], "total" => [] ];

        foreach ( $proyeks as $proyek )
        {
            $charts[ "current" ][ $proyek->nama ] = $this->calculateProjectTotals (
                $data[ "atbDataCurrent" ],
                $data[ "apbDataCurrent" ],
                $data[ "saldoDataCurrent" ],
                $proyek->id
            );

            $charts[ "total" ][ $proyek->nama ] = $this->calculateProjectTotals (
                $data[ "atbDataTotal" ],
                $data[ "apbDataTotal" ],
                $data[ "saldoDataTotal" ],
                $proyek->id
            );
        }

        return $charts;
    }

    private function calculateProjectTotals (
        $atbData,
        $apbData,
        $saldoData,
        $proyekId
    ) {
        return [ 
            "penerimaan"  => $this->sumProjectData ( $atbData, $proyekId ),
            "pengeluaran" => $this->sumProjectData ( $apbData, $proyekId, true ),
            "saldo"       => $this->sumProjectData ( $saldoData, $proyekId ),
        ];
    }

    private function sumProjectData ( $data, $proyekId, $isApb = false )
    {
        $filteredData = $data->where ( "id_proyek", $proyekId );

        if ( $isApb )
        {
            $filteredData = $filteredData->whereNotIn ( "status", [ 
                "pending",
                "rejected",
            ] );

            return $filteredData->sum ( function ($item)
            {
                return $item->quantity * ( $item->saldo->harga ?? 0 );
            } );
        }
        else
        {
            // For ATB data
            return $filteredData->sum ( function ($item)
            {
                return $item->quantity * ( $item->harga ?? 0 );
            } );
        }
    }

    private function calculateChartData ( $atbData, $apbData, $saldoData )
    {
        $chartData = [];

        foreach ( self::CATEGORIES as $category )
        {
            $jenis = $category[ "jenis" ];
            if ( ! isset ( $chartData[ $jenis ] ) )
            {
                $chartData[ $jenis ] = [ "atb" => 0, "apb" => 0, "saldo" => 0 ];
            }

            $categoryTotal                  = $this->calculateTotal ( $atbData, $category );
            $chartData[ $jenis ][ "atb" ] += $categoryTotal;
            $chartData[ $jenis ][ "apb" ] += $this->calculateTotal (
                $apbData,
                $category
            );
            $chartData[ $jenis ][ "saldo" ] += $this->calculateTotal (
                $saldoData,
                $category
            );
        }

        return $chartData;
    }

    private function calculateTotal ( Collection $items, array $category )
    {
        return $items
            ->filter (
                fn ( $item ) => $item->masterDataSparepart->kategoriSparepart
                    ->kode === $category[ "kode" ]
            )
            ->sum (
                fn ( $item ) => $item->quantity *
                ( $item->saldo->harga ?? ( $item->harga ?? 0 ) )
            );
    }

    private function calculateOverallTotal ( Collection $data )
    {
        return $data
            ->filter ( fn ( $item ) => in_array ( $item->tipe, self::VALID_TYPES ) )
            ->sum ( function ($item)
            {
                if ( $item instanceof APB )
                {
                    return ! in_array ( $item->status, [ "pending", "rejected" ] ) &&
                        $item->saldo
                        ? $item->quantity * $item->saldo->harga
                        : 0;
                }

                // Calculate the raw value - don't use abs() for saldo items
                // This will allow negative values to remain negative
                $value = $item->quantity *
                    ( $item->saldo->harga ?? ( $item->harga ?? 0 ) );

                return $value;
            } );
    }

    // Helper to identify saldo items
    private function isSaldoItem ( $item )
    {
        // Check if it's a Saldo model or has a specific property indicating it's a saldo
        return $item instanceof Saldo ||
            ( isset ( $item->tipe ) && strpos ( strtolower ( $item->tipe ), 'saldo' ) !== false );
    }

    private function getCategoryDataForProject ( $projectId, $atbCurrent, $apbCurrent, $saldoCurrent, $atbTotal, $apbTotal, $saldoTotal )
    {
        $categories  = KategoriSparepart::all ();
        $currentData = [];
        $totalData   = [];

        foreach ( $categories as $category )
        {
            // Calculate current period totals
            $currentAtb = $atbCurrent
                ->filter ( function ($item) use ($category)
                {
                    return $item->masterDataSparepart &&
                        $item->masterDataSparepart->id_kategori_sparepart == $category->id;
                } )
                ->sum ( function ($item)
                {
                    return $item->quantity * ( $item->harga ?? 0 );
                } );

            $currentApb = $apbCurrent
                ->filter ( function ($item) use ($category)
                {
                    return $item->masterDataSparepart &&
                        $item->masterDataSparepart->id_kategori_sparepart == $category->id &&
                        ! in_array ( $item->status, [ 'pending', 'rejected' ] );
                } )
                ->sum ( function ($item)
                {
                    return $item->quantity * ( $item->saldo->harga ?? 0 );
                } );

            $currentSaldo = $saldoCurrent
                ->filter ( function ($item) use ($category)
                {
                    return $item->masterDataSparepart &&
                        $item->masterDataSparepart->id_kategori_sparepart == $category->id;
                } )
                ->sum ( function ($item)
                {
                    return $item->quantity * ( $item->harga ?? 0 );
                } );

            // Calculate total period totals
            $totalAtb = $atbTotal
                ->filter ( function ($item) use ($category)
                {
                    return $item->masterDataSparepart &&
                        $item->masterDataSparepart->id_kategori_sparepart == $category->id;
                } )
                ->sum ( function ($item)
                {
                    return $item->quantity * ( $item->harga ?? 0 );
                } );

            $totalApb = $apbTotal
                ->filter ( function ($item) use ($category)
                {
                    return $item->masterDataSparepart &&
                        $item->masterDataSparepart->id_kategori_sparepart == $category->id &&
                        ! in_array ( $item->status, [ 'pending', 'rejected' ] );
                } )
                ->sum ( function ($item)
                {
                    return $item->quantity * ( $item->saldo->harga ?? 0 );
                } );

            $totalSaldo = $saldoTotal
                ->filter ( function ($item) use ($category)
                {
                    return $item->masterDataSparepart &&
                        $item->masterDataSparepart->id_kategori_sparepart == $category->id;
                } )
                ->sum ( function ($item)
                {
                    return $item->quantity * ( $item->harga ?? 0 );
                } );

            // Include all categories regardless of their values
            $currentData[ $category->nama ] = [ 
                'ATB'   => $currentAtb,
                'APB'   => $currentApb,
                'Saldo' => $currentSaldo
            ];

            $totalData[ $category->nama ] = [ 
                'ATB'   => $totalAtb,
                'APB'   => $totalApb,
                'Saldo' => $totalSaldo
            ];
        }

        // Sort by highest total value
        $currentData = $this->sortCategoryData ( $currentData );
        $totalData   = $this->sortCategoryData ( $totalData );

        return [ 
            'current' => $currentData,
            'total'   => $totalData
        ];
    }

    private function sortCategoryData ( $data )
    {
        uasort ( $data, function ($a, $b)
        {
            $totalA = $a[ 'ATB' ] + $a[ 'APB' ];
            $totalB = $b[ 'ATB' ] + $b[ 'APB' ];
            return $totalB <=> $totalA;
        } );
        return $data;
    }

    // Add these helper methods for clearer ATB and APB calculations
    private function calculateATBTotal ( Collection $data )
    {
        return $data
            ->filter ( fn ( $item ) => in_array ( $item->tipe, self::VALID_TYPES ) )
            ->sum ( function ($item)
            {
                return $item->quantity * ( $item->harga ?? 0 );
            } );
    }

    private function calculateAPBTotal ( Collection $data )
    {
        return $data
            ->filter ( fn ( $item ) => in_array ( $item->tipe, self::VALID_TYPES ) )
            ->sum ( function ($item)
            {
                // Only count APB items that aren't pending or rejected
                if ( in_array ( $item->status, [ "pending", "rejected" ] ) )
                {
                    return 0;
                }
                return $item->quantity * ( $item->saldo->harga ?? 0 );
            } );
    }

    // Updated method to calculate monthly financial data with PostgreSQL compatibility
    // and correct date ranges (26th of previous month to 25th of current month)
    private function calculateMonthlyFinancialData ( $projectId = null )
    {
        $currentYear = now ()->year;
        $monthlyData = [];
        $monthNames  = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];

        // For each month, calculate values using custom date ranges
        for ( $month = 1; $month <= 12; $month++ )
        {
            // Calculate start date (26th of previous month)
            $startDate = Carbon::create ( $currentYear, $month, 1 )->subDays ( 5 );
            if ( $month == 1 )
            {
                // For January, start date is December 26 of previous year
                $startDate = Carbon::create ( $currentYear - 1, 12, 26 );
            }
            else
            {
                // For other months, start date is 26th of previous month
                $startDate = Carbon::create ( $currentYear, $month - 1, 26 );
            }

            // Calculate end date (25th of current month)
            $endDate = Carbon::create ( $currentYear, $month, 25 );

            // Base query for ATB with explicit table name prefixes
            $atbQuery = ATB::where ( 'atb.tanggal', '>=', $startDate )
                ->where ( 'atb.tanggal', '<=', $endDate )
                ->whereIn ( 'atb.tipe', self::VALID_TYPES );

            // Apply project filter if provided
            if ( $projectId )
            {
                $atbQuery->where ( 'atb.id_proyek', $projectId );
            }

            // Calculate ATB total for this month
            $atbValue = $atbQuery->sum ( \DB::raw ( 'atb.quantity * atb.harga' ) );

            // Base query for APB with explicit table name prefixes
            $apbQuery = APB::where ( 'apb.tanggal', '>=', $startDate )
                ->where ( 'apb.tanggal', '<=', $endDate )
                ->whereNotIn ( 'apb.status', [ 'pending', 'rejected' ] )
                ->whereIn ( 'apb.tipe', self::VALID_TYPES )
                ->join ( 'saldo', 'saldo.id', '=', 'apb.id_saldo' );

            // Apply project filter if provided
            if ( $projectId )
            {
                $apbQuery->where ( 'apb.id_proyek', $projectId );
            }

            // Calculate APB total for this month
            $apbValue = $apbQuery->sum ( \DB::raw ( 'apb.quantity * saldo.harga' ) );

            // Add data to the monthly arrays
            $monthlyData[ 'atb' ][] = [ 
                'month' => $monthNames[ $month - 1 ],
                'value' => (float) $atbValue
            ];

            $monthlyData[ 'apb' ][] = [ 
                'month' => $monthNames[ $month - 1 ],
                'value' => (float) $apbValue
            ];

            $monthlyData[ 'saldo' ][] = [ 
                'month' => $monthNames[ $month - 1 ],
                'value' => (float) ( $atbValue - $apbValue )
            ];
        }

        return $monthlyData;
    }
}
