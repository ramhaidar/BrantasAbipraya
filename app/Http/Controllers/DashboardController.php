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
        $user      = Auth::user ();
        $id_proyek = $request->query ( "id_proyek" );
        $proyeks   = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        // Date ranges
        $currentDate = now ();
        $startDate   = $currentDate->copy ()->startOfMonth ();
        $endDate     = $currentDate->copy ()->endOfMonth ();

        // Build base queries
        [ $atbQuery, $apbQuery, $saldoQuery ] = $this->buildBaseQueries (
            $user,
            $id_proyek
        );

        // Get data for different date ranges
        $data = $this->getQueriesData (
            $atbQuery,
            $apbQuery,
            $saldoQuery,
            $startDate,
            $endDate
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

        return view ( "dashboard.dashboard.dashboard", [ 
            "headerPage"             => "Dashboard",
            "page"                   => "Dashboard",

            "proyeks"                => $proyeks,
            "selectedProject"        => $id_proyek,

            "totalATB"               => $this->calculateOverallTotal ( $data[ "atbData" ] ),
            "totalAPB"               => $this->calculateOverallTotal ( $data[ "apbData" ] ),
            "totalSaldo"             => $this->calculateOverallTotal ( $data[ "saldoData" ] ),

            "chartData"              => $chartData[ "main" ],
            "chartDataCurrent"       => $chartData[ "current" ],
            "chartDataTotal"         => $chartData[ "total" ],

            "startDate"              => $startDate->format ( "Y-m-d" ),
            "endDate"                => $endDate->format ( "Y-m-d" ),

            "horizontalChartCurrent" => $horizontalCharts[ "current" ],
            "horizontalChartTotal"   => $horizontalCharts[ "total" ],
            "categoryData"           => $categoryData,
        ] );
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

        // Build all queries using the base queries
        return [ 
            "atbData"          => ( clone $atbBase )->get (),
            "apbData"          => ( clone $apbBase )->get (),
            "saldoData"        => ( clone $saldoBase )->get (),

            "atbDataCurrent"   => ( clone $atbBase )
                ->whereBetween ( "tanggal", [ $startDate, $endDate ] )
                ->get (),
            "apbDataCurrent"   => ( clone $apbBase )
                ->whereBetween ( "tanggal", [ $startDate, $endDate ] )
                ->get (),
            "saldoDataCurrent" => ( clone $saldoBase )
                ->whereHas (
                    "atb",
                    fn ( $q ) =>
                    $q->whereBetween ( "tanggal", [ $startDate, $endDate ] )
                )
                ->get (),

            "atbDataTotal"     => ( clone $atbBase )
                ->where ( "tanggal", "<=", $endDate )
                ->get (),
            "apbDataTotal"     => ( clone $apbBase )
                ->where ( "tanggal", "<=", $endDate )
                ->get (),
            "saldoDataTotal"   => ( clone $saldoBase )
                ->whereHas (
                    "atb",
                    fn ( $q ) =>
                    $q->where ( "tanggal", "<=", $endDate )
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
        }
        return $filteredData->sum (
            fn ( $item ) => $item->quantity *
            ( $item->saldo->harga ?? ( $item->harga ?? 0 ) )
        );
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
                return $item->quantity *
                    ( $item->saldo->harga ?? ( $item->harga ?? 0 ) );
            } );
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
}
