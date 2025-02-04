<?php

namespace App\Http\Controllers;

use App\Models\APB;
use App\Models\ATB;
use App\Models\Alat;
use App\Models\User;
use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

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
            ->latest ( "updated_at" )
            ->latest ( "id" )
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

        // Calculate chart data
        $chartData = $this->calculateAllChartData ( $data );

        // Calculate horizontal charts
        $horizontalCharts = $this->calculateHorizontalCharts ( $proyeks, $data );

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
        ] );
    }

    private function buildBaseQueries ( $user, $id_proyek )
    {
        $atbQuery   = ATB::query ();
        $apbQuery   = APB::with ( "saldo" );
        $saldoQuery = Saldo::query ();

        if ( $id_proyek )
        {
            if (
                $user->role !== "Admin" &&
                ! $user
                    ->proyek ()
                    ->where ( "proyek.id", $id_proyek )
                    ->exists ()
            )
            {
                abort ( 403, "Unauthorized access to this project" );
            }
            $this->applyProjectFilter (
                [ $atbQuery, $apbQuery, $saldoQuery ],
                $id_proyek
            );
        }
        elseif ( $user->role !== "Admin" )
        {
            $userProyekIds = $user->proyek ()->pluck ( "id" );
            $this->applyUserProjectsFilter (
                [ $atbQuery, $apbQuery, $saldoQuery ],
                $userProyekIds
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

    private function getQueriesData (
        $atbQuery,
        $apbQuery,
        $saldoQuery,
        $startDate,
        $endDate
    ) {
        return [ 
            "atbData"          => clone $atbQuery->get (),
            "apbData"          => clone $apbQuery->get (),
            "saldoData"        => clone $saldoQuery->get (),
            "atbDataCurrent"   => clone $atbQuery
                ->whereBetween ( "tanggal", [ $startDate, $endDate ] )
                ->get (),
            "apbDataCurrent"   => clone $apbQuery
                ->whereBetween ( "tanggal", [ $startDate, $endDate ] )
                ->get (),
            "saldoDataCurrent" => clone $saldoQuery
                ->whereHas (
                    "atb",
                    fn ( $q ) => $q->whereBetween ( "tanggal", [ 
                        $startDate,
                        $endDate,
                    ] )
                )
                ->get (),
            "atbDataTotal"     => clone $atbQuery
                ->where ( "tanggal", "<=", $endDate )
                ->get (),
            "apbDataTotal"     => clone $apbQuery
                ->where ( "tanggal", "<=", $endDate )
                ->get (),
            "saldoDataTotal"   => clone $saldoQuery
                ->whereHas (
                    "atb",
                    fn ( $q ) => $q->where ( "tanggal", "<=", $endDate )
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

            $categoryTotal              = $this->calculateTotal ( $atbData, $category );
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
}
