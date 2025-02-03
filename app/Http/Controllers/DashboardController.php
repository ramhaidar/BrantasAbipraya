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
class DashboardController extends Controller
{
    public function index ( Request $request )
    {
        $user      = Auth::user ();
        $id_proyek = $request->query ( 'id_proyek' );

        // Fetch projects
        $proyeks = Proyek::with ( "users" )
            ->latest ( "updated_at" )
            ->latest ( "id" )
            ->get ();

        // Get date ranges
        $currentDate = now ();
        $startDate   = $currentDate->copy ()->startOfMonth ();
        $endDate     = $currentDate->copy ()->endOfMonth ();

        // Base queries with date range conditions
        $atbQueryCurrent   = ATB::whereBetween ( 'tanggal', [ $startDate, $endDate ] );
        $apbQueryCurrent   = APB::whereBetween ( 'tanggal', [ $startDate, $endDate ] );
        $saldoQueryCurrent = Saldo::whereHas ( 'atb', fn ( $q ) => $q->whereBetween ( 'tanggal', [ $startDate, $endDate ] ) );

        $atbQueryTotal   = ATB::where ( 'tanggal', '<=', $endDate );
        $apbQueryTotal   = APB::where ( 'tanggal', '<=', $endDate );
        $saldoQueryTotal = Saldo::whereHas ( 'atb', fn ( $q ) => $q->where ( 'tanggal', '<=', $endDate ) );

        // Base queries
        $atbQuery   = ATB::query ();
        $apbQuery   = APB::with ( 'saldo' );
        $saldoQuery = Saldo::query ();

        // Filter projects based on role
        if ( $id_proyek )
        {
            if ( $user->role !== 'Admin' && ! $user->proyek ()->where ( 'proyek.id', $id_proyek )->exists () )
            {
                abort ( 403, 'Unauthorized access to this project' );
            }
            $atbQuery->where ( 'id_proyek', $id_proyek );
            $apbQuery->where ( 'id_proyek', $id_proyek );
            $saldoQuery->whereHas ( 'atb', fn ( $query ) => $query->where ( 'id_proyek', $id_proyek ) );

            $atbQueryCurrent->where ( 'id_proyek', $id_proyek );
            $apbQueryCurrent->where ( 'id_proyek', $id_proyek );
            $saldoQueryCurrent->whereHas ( 'atb', fn ( $q ) => $q->where ( 'id_proyek', $id_proyek ) );

            $atbQueryTotal->where ( 'id_proyek', $id_proyek );
            $apbQueryTotal->where ( 'id_proyek', $id_proyek );
            $saldoQueryTotal->whereHas ( 'atb', fn ( $q ) => $q->where ( 'id_proyek', $id_proyek ) );
        }
        elseif ( $user->role !== 'Admin' )
        {
            $userProyekIds = $user->proyek ()->pluck ( 'id' );
            $atbQuery->whereIn ( 'id_proyek', $userProyekIds );
            $apbQuery->whereIn ( 'id_proyek', $userProyekIds );
            $saldoQuery->whereHas ( 'atb', fn ( $query ) => $query->whereIn ( 'id_proyek', $userProyekIds ) );

            $userProyekIds = $user->proyek ()->pluck ( 'id' );
            // Apply to both current and total queries
            $atbQueryCurrent->whereIn ( 'id_proyek', $userProyekIds );
            $apbQueryCurrent->whereIn ( 'id_proyek', $userProyekIds );
            $saldoQueryCurrent->whereHas ( 'atb', fn ( $q ) => $q->whereIn ( 'id_proyek', $userProyekIds ) );

            $atbQueryTotal->whereIn ( 'id_proyek', $userProyekIds );
            $apbQueryTotal->whereIn ( 'id_proyek', $userProyekIds );
            $saldoQueryTotal->whereHas ( 'atb', fn ( $q ) => $q->whereIn ( 'id_proyek', $userProyekIds ) );
        }

        // Preload data to avoid repeated queries
        $atbData   = $atbQuery->get ();
        $apbData   = $apbQuery->get ();
        $saldoData = $saldoQuery->get ();

        // Get data
        $atbDataCurrent   = $atbQueryCurrent->get ();
        $apbDataCurrent   = $apbQueryCurrent->with ( 'saldo' )->get ();
        $saldoDataCurrent = $saldoQueryCurrent->get ();

        $atbDataTotal   = $atbQueryTotal->get ();
        $apbDataTotal   = $apbQueryTotal->with ( 'saldo' )->get ();
        $saldoDataTotal = $saldoQueryTotal->get ();

        // Define category data
        $categories = [ 
            [ 'kode' => 'A1', 'nama' => 'CABIN', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A2', 'nama' => 'ENGINE SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A3', 'nama' => 'TRANSMISSION SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A4', 'nama' => 'CHASSIS & SWING MACHINERY', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A5', 'nama' => 'DIFFERENTIAL SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A6', 'nama' => 'ELECTRICAL SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A7', 'nama' => 'HYDRAULIC/PNEUMATIC SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A8', 'nama' => 'STEERING SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A9', 'nama' => 'BRAKE SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A10', 'nama' => 'SUSPENSION', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A11', 'nama' => 'WORK EQUIPMENT', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A12', 'nama' => 'UNDERCARRIAGE', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A13', 'nama' => 'FINAL DRIVE', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A14', 'nama' => 'FREIGHT COST', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'B11', 'nama' => 'Oil Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B12', 'nama' => 'Fuel Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B13', 'nama' => 'Air Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B21', 'nama' => 'Engine Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B22', 'nama' => 'Hydraulic Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B3', 'nama' => 'TYRE', 'jenis' => 'Pemeliharaan' ],
            [ 'kode' => 'C1', 'nama' => 'WORKSHOP', 'jenis' => 'Material' ] // Changed from 'Workshop' to 'Material'
        ];

        // Function to calculate totals per category
        function calculateTotal ( $items, $category )
        {
            return $items->where ( 'masterDataSparepart.kategoriSparepart.kode', $category[ 'kode' ] )
                ->sum ( fn ( $item ) => $item->quantity * ( $item->saldo->harga ?? $item->harga ?? 0 ) );
        }

        // Aggregate chart data
        $chartData = [];
        foreach ( $categories as $category )
        {
            $jenis = $category[ 'jenis' ];

            $chartData[ $jenis ][ 'atb' ]   = ( $chartData[ $jenis ][ 'atb' ] ?? 0 ) + calculateTotal ( $atbData, $category );
            $chartData[ $jenis ][ 'apb' ]   = ( $chartData[ $jenis ][ 'apb' ] ?? 0 ) + calculateTotal ( $apbData, $category );
            $chartData[ $jenis ][ 'saldo' ] = ( $chartData[ $jenis ][ 'saldo' ] ?? 0 ) + calculateTotal ( $saldoData, $category );
        }

        // Calculate chart data for both periods
        $chartDataCurrent = $this->calculateChartData ( $atbDataCurrent, $apbDataCurrent, $saldoDataCurrent );
        $chartDataTotal   = $this->calculateChartData ( $atbDataTotal, $apbDataTotal, $saldoDataTotal );

        // Calculate overall totals
        function calculateOverallTotal ( $data )
        {
            return $data->sum ( fn ( $item ) => in_array ( $item->tipe, [ 'hutang-unit-alat', 'panjar-unit-alat', 'mutasi-proyek', 'panjar-proyek' ] )
                ? $item->quantity * ( $item->saldo->harga ?? $item->harga ?? 0 )
                : 0 );
        }

        // Add new data for horizontal charts
        $horizontalChartCurrent = [];
        $horizontalChartTotal   = [];

        foreach ( $proyeks as $proyek )
        {
            // Current month data
            $atbCurrentTotal = $atbDataCurrent
                ->where ( 'id_proyek', $proyek->id )
                ->sum ( fn ( $item ) => $item->quantity * $item->harga );

            $apbCurrentTotal = $apbDataCurrent
                ->where ( 'id_proyek', $proyek->id )
                ->whereNotIn ( 'status', [ 'pending', 'rejected' ] )
                ->sum ( fn ( $item ) => $item->quantity * ( $item->saldo->harga ?? 0 ) );

            $saldoCurrentTotal = $saldoDataCurrent
                ->where ( 'id_proyek', $proyek->id )
                ->sum ( fn ( $item ) => $item->quantity * $item->harga );

            $horizontalChartCurrent[ $proyek->nama ] = [ 
                'penerimaan'  => $atbCurrentTotal,
                'pengeluaran' => $apbCurrentTotal,
                'saldo'       => $saldoCurrentTotal
            ];

            // Total to date data
            $atbTotal = $atbDataTotal
                ->where ( 'id_proyek', $proyek->id )
                ->sum ( fn ( $item ) => $item->quantity * $item->harga );

            $apbTotal = $apbDataTotal
                ->where ( 'id_proyek', $proyek->id )
                ->whereNotIn ( 'status', [ 'pending', 'rejected' ] )
                ->sum ( fn ( $item ) => $item->quantity * ( $item->saldo->harga ?? 0 ) );

            $saldoTotal = $saldoDataTotal
                ->where ( 'id_proyek', $proyek->id )
                ->sum ( fn ( $item ) => $item->quantity * $item->harga );

            $horizontalChartTotal[ $proyek->nama ] = [ 
                'penerimaan'  => $atbTotal,
                'pengeluaran' => $apbTotal,
                'saldo'       => $saldoTotal
            ];
        }

        return view ( 'dashboard.dashboard.dashboard', [ 
            'headerPage'             => 'Dashboard',
            'page'                   => 'Dashboard',

            'proyeks'                => $proyeks,
            'selectedProject'        => $id_proyek,
            'totalATB'               => calculateOverallTotal ( $atbData ),
            'totalAPB'               => calculateOverallTotal ( $apbData ),
            'totalSaldo'             => calculateOverallTotal ( $saldoData ),
            'chartData'              => $chartData,
            'chartDataCurrent'       => $chartDataCurrent,
            'chartDataTotal'         => $chartDataTotal,
            'startDate'              => $startDate->format ( 'Y-m-d' ),
            'endDate'                => $endDate->format ( 'Y-m-d' ),
            'horizontalChartCurrent' => $horizontalChartCurrent,
            'horizontalChartTotal'   => $horizontalChartTotal,
        ] );
    }

    private function calculateChartData ( $atbData, $apbData, $saldoData )
    {
        // Move categories array to class property or configuration
        $categories = [ 
            [ 'kode' => 'A1', 'nama' => 'CABIN', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A2', 'nama' => 'ENGINE SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A3', 'nama' => 'TRANSMISSION SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A4', 'nama' => 'CHASSIS & SWING MACHINERY', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A5', 'nama' => 'DIFFERENTIAL SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A6', 'nama' => 'ELECTRICAL SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A7', 'nama' => 'HYDRAULIC/PNEUMATIC SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A8', 'nama' => 'STEERING SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A9', 'nama' => 'BRAKE SYSTEM', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A10', 'nama' => 'SUSPENSION', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A11', 'nama' => 'WORK EQUIPMENT', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A12', 'nama' => 'UNDERCARRIAGE', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A13', 'nama' => 'FINAL DRIVE', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'A14', 'nama' => 'FREIGHT COST', 'jenis' => 'Perbaikan' ],
            [ 'kode' => 'B11', 'nama' => 'Oil Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B12', 'nama' => 'Fuel Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B13', 'nama' => 'Air Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B21', 'nama' => 'Engine Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B22', 'nama' => 'Hydraulic Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B3', 'nama' => 'TYRE', 'jenis' => 'Pemeliharaan' ],
            [ 'kode' => 'C1', 'nama' => 'WORKSHOP', 'jenis' => 'Material' ] // Changed from 'Workshop' to 'Material'
        ];

        $chartData = [];
        foreach ( $categories as $category )
        {
            $jenis = $category[ 'jenis' ];
            if ( ! isset ( $chartData[ $jenis ] ) )
            {
                $chartData[ $jenis ] = [ 
                    'atb'   => 0,
                    'apb'   => 0,
                    'saldo' => 0
                ];
            }

            // Calculate totals for this category
            $chartData[ $jenis ][ 'atb' ] += $this->calculateTotal ( $atbData, $category );
            $chartData[ $jenis ][ 'apb' ] += $this->calculateTotal ( $apbData, $category );
            $chartData[ $jenis ][ 'saldo' ] += $this->calculateTotal ( $saldoData, $category );
        }

        return $chartData;
    }

    private function calculateTotal ( $items, $category )
    {
        return $items->filter ( function ($item) use ($category)
        {
            return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
        } )->sum ( function ($item)
        {
            return $item->quantity * ( $item->saldo->harga ?? $item->harga ?? 0 );
        } );
    }

    private function calculateOverallTotal ( $data )
    {
        $total = 0;
        foreach ( $data as $item )
        {
            if ( in_array ( $item->tipe, [ 'hutang-unit-alat', 'panjar-unit-alat', 'mutasi-proyek', 'panjar-proyek' ] ) )
            {
                // For APB items
                if ( $item instanceof APB )
                {
                    if ( ! in_array ( $item->status, [ 'pending', 'rejected' ] ) && $item->saldo )
                    {
                        $total += $item->quantity * $item->saldo->harga;
                    }
                }
                // For ATB items
                elseif ( $item instanceof ATB )
                {
                    $total += $item->quantity * $item->harga;
                }
                // For Saldo items
                elseif ( $item instanceof Saldo )
                {
                    $total += $item->quantity * $item->harga;
                }
            }
        }
        return $total;
    }

}