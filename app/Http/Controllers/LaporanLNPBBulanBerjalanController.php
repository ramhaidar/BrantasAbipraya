<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\APB;
use App\Models\ATB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Saldo;  // Add this import

class LaporanLNPBBulanBerjalanController extends Controller
{
    public function index ( Request $request )
    {
        $proyek = Proyek::with ( "users" )->find ( $request->id_proyek );

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

        // +++
        $data = [ 
            [ 'kode' => 'A1', 'nama' => 'CABIN', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A2', 'nama' => 'ENGINE SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A3', 'nama' => 'TRANSMISSION SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A4', 'nama' => 'CHASSIS & SWING MACHINERY', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A5', 'nama' => 'DIFFERENTIAL SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A6', 'nama' => 'ELECTRICAL SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A7', 'nama' => 'HYDRAULIC/PNEUMATIC SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A8', 'nama' => 'STEERING SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A9', 'nama' => 'BRAKE SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A10', 'nama' => 'SUSPENSION', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A11', 'nama' => 'WORK EQUIPMENT', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A12', 'nama' => 'UNDERCARRIAGE', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A13', 'nama' => 'FINAL DRIVE', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A14', 'nama' => 'FREIGHT COST', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'B11', 'nama' => 'Oil Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B12', 'nama' => 'Fuel Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B13', 'nama' => 'Air Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B14', 'nama' => 'Hydraulic Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B15', 'nama' => 'Transmission Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B16', 'nama' => 'Differential Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B21', 'nama' => 'Engine Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B22', 'nama' => 'Hydraulic Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B23', 'nama' => 'Transmission Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B24', 'nama' => 'Final Drive Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B25', 'nama' => 'Swing & Damper Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B26', 'nama' => 'Differential Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B27', 'nama' => 'Grease', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B28', 'nama' => 'Brake & Power Steering Fluid', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B29', 'nama' => 'Coolant', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B3', 'nama' => 'TYRE', 'jenis' => 'Pemeliharaan', 'subJenis' => null ],
            [ 'kode' => 'C1', 'nama' => 'WORKSHOP', 'jenis' => 'Workshop', 'subJenis' => null ],
        ];
        // +++

        // === Calculate ATB, APB, and Saldo === //
        $currentDate      = now ();
        $defaultStartDate = $currentDate->copy ()->subMonth ()->day ( 26 );
        $defaultEndDate   = $currentDate->copy ()->day ( 25 );

        // Validate and parse dates with error handling
        try
        {
            $startDate = $request->filled ( 'startDate' ) && $request->startDate !== '-NaN-26'
                ? Carbon::parse ( $request->startDate )
                : $defaultStartDate;

            $endDate = $request->filled ( 'endDate' ) && $request->endDate !== '-25'
                ? Carbon::parse ( $request->endDate )
                : $defaultEndDate;

            // Ensure startDate is on the 26th and endDate is on the 25th
            $startDate = $startDate->day ( 26 );
            $endDate   = $endDate->day ( 25 );
        }
        catch ( \Exception $e )
        {
            // If date parsing fails, use defaults
            $startDate = $defaultStartDate;
            $endDate   = $defaultEndDate;
        }

        $ATB = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        $APB = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        $SALDO = Saldo::with ( 'masterDataSparepart.KategoriSparepart', 'atb' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereHas ( 'atb', function ($query) use ($startDate, $endDate)
            {
                $query->whereBetween ( 'tanggal', [ $startDate, $endDate ] );
            } )
            ->get ();

        $sums = [];
        foreach ( $data as $category )
        {
            // ATB Calculations
            $categoryItemsATB = $ATB->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // APB Calculations
            $categoryItemsAPB = $APB->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // New direct Saldo calculations
            $categoryItemsSaldo = $SALDO->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            $sums[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => [ 
                    'hutang-unit-alat' => $categoryItemsATB->where ( 'tipe', 'hutang-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'panjar-unit-alat' => $categoryItemsATB->where ( 'tipe', 'panjar-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'mutasi-proyek'    => $categoryItemsATB->where ( 'tipe', 'mutasi-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'panjar-proyek'    => $categoryItemsATB->where ( 'tipe', 'panjar-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } )
                ],
                'apb'      => [ 
                    'hutang-unit-alat' => $categoryItemsAPB->where ( 'tipe', 'hutang-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->saldo->harga;
                    } ),
                    'panjar-unit-alat' => $categoryItemsAPB->where ( 'tipe', 'panjar-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->saldo->harga;
                    } ),
                    'mutasi-proyek'    => $categoryItemsAPB->where ( 'tipe', 'mutasi-proyek' )->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
                    {
                        return $item->quantity * $item->saldo->harga;
                    } ),
                    'panjar-proyek'    => $categoryItemsAPB->where ( 'tipe', 'panjar-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->saldo->harga;
                    } )
                ],
                'saldo'    => [ 
                    'hutang-unit-alat' => $categoryItemsSaldo->where ( 'tipe', 'hutang-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'panjar-unit-alat' => $categoryItemsSaldo->where ( 'tipe', 'panjar-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'mutasi-proyek'    => $categoryItemsSaldo->where ( 'tipe', 'mutasi-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'panjar-proyek'    => $categoryItemsSaldo->where ( 'tipe', 'panjar-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } )
                ]
            ];

            // Calculate totals
            $sums[ $category[ 'kode' ] ][ 'atb' ][ 'total' ]   = array_sum ( array_filter ( $sums[ $category[ 'kode' ] ][ 'atb' ], 'is_numeric' ) );
            $sums[ $category[ 'kode' ] ][ 'apb' ][ 'total' ]   = array_sum ( array_filter ( $sums[ $category[ 'kode' ] ][ 'apb' ], 'is_numeric' ) );
            $sums[ $category[ 'kode' ] ][ 'saldo' ][ 'total' ] = array_sum ( array_filter ( $sums[ $category[ 'kode' ] ][ 'saldo' ], 'is_numeric' ) );
        }

        // dd ( $sums );

        // Pass the calculated sums to the view
        return view ( 'dashboard.laporan.bulan_berjalan.bulan_berjalan', [ 
            'proyek'     => $proyek,
            'proyeks'    => $proyeks,
            'sums'       => $sums,
            'startDate'  => $startDate,
            'endDate'    => $endDate,

            'headerPage' => $proyek->nama,
            'page'       => 'LNPB Bulan Berjalan',
        ] );
    }

    public function semuaBulanBerjalan_index ( Request $request )
    {
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

        // Default date calculations
        $currentDate      = now ();
        $defaultStartDate = $currentDate->copy ()->subMonth ()->day ( 26 );
        $defaultEndDate   = $currentDate->copy ()->day ( 25 );

        // Validate and parse dates with error handling
        try
        {
            $startDate = $request->filled ( 'startDate' ) && $request->startDate !== '-NaN-26'
                ? Carbon::parse ( $request->startDate )
                : $defaultStartDate;

            $endDate = $request->filled ( 'endDate' ) && $request->endDate !== '-25'
                ? Carbon::parse ( $request->endDate )
                : $defaultEndDate;

            // Ensure startDate is on the 26th and endDate is on the 25th
            $startDate = $startDate->day ( 26 );
            $endDate   = $endDate->day ( 25 );
        }
        catch ( \Exception $e )
        {
            // If date parsing fails, use defaults
            $startDate = $defaultStartDate;
            $endDate   = $defaultEndDate;
        }

        // +++
        $data = [ 
            [ 'kode' => 'A1', 'nama' => 'CABIN', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A2', 'nama' => 'ENGINE SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A3', 'nama' => 'TRANSMISSION SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A4', 'nama' => 'CHASSIS & SWING MACHINERY', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A5', 'nama' => 'DIFFERENTIAL SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A6', 'nama' => 'ELECTRICAL SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A7', 'nama' => 'HYDRAULIC/PNEUMATIC SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A8', 'nama' => 'STEERING SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A9', 'nama' => 'BRAKE SYSTEM', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A10', 'nama' => 'SUSPENSION', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A11', 'nama' => 'WORK EQUIPMENT', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A12', 'nama' => 'UNDERCARRIAGE', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A13', 'nama' => 'FINAL DRIVE', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'A14', 'nama' => 'FREIGHT COST', 'jenis' => 'Perbaikan', 'subJenis' => null ],
            [ 'kode' => 'B11', 'nama' => 'Oil Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B12', 'nama' => 'Fuel Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B13', 'nama' => 'Air Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B14', 'nama' => 'Hydraulic Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B15', 'nama' => 'Transmission Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B16', 'nama' => 'Differential Filter', 'jenis' => 'Pemeliharaan', 'subJenis' => 'MAINTENANCE KIT' ],
            [ 'kode' => 'B21', 'nama' => 'Engine Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B22', 'nama' => 'Hydraulic Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B23', 'nama' => 'Transmission Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B24', 'nama' => 'Final Drive Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B25', 'nama' => 'Swing & Damper Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B26', 'nama' => 'Differential Oil', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B27', 'nama' => 'Grease', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B28', 'nama' => 'Brake & Power Steering Fluid', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B29', 'nama' => 'Coolant', 'jenis' => 'Pemeliharaan', 'subJenis' => 'OIL & LUBRICANTS' ],
            [ 'kode' => 'B3', 'nama' => 'TYRE', 'jenis' => 'Pemeliharaan', 'subJenis' => null ],
            [ 'kode' => 'C1', 'nama' => 'WORKSHOP', 'jenis' => 'Workshop', 'subJenis' => null ],
        ];
        // +++

        // === Calculate ATB, APB, and Saldo === //
        $currentDate      = now ();
        $defaultStartDate = $currentDate->copy ()->subMonth ()->day ( 26 );
        $defaultEndDate   = $currentDate->copy ()->day ( 25 );

        // Use request dates if provided, otherwise use defaults
        $startDate = $request->filled ( 'startDate' ) ? Carbon::parse ( $request->startDate ) : $defaultStartDate;
        $endDate   = $request->filled ( 'endDate' ) ? Carbon::parse ( $request->endDate ) : $defaultEndDate;

        $ATB = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        $APB = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        $SALDO = Saldo::with ( 'masterDataSparepart.KategoriSparepart', 'atb' )
            ->whereHas ( 'atb', function ($query) use ($startDate, $endDate)
            {
                $query->whereBetween ( 'tanggal', [ $startDate, $endDate ] );
            } )
            ->get ();

        $sums = [];
        foreach ( $data as $category )
        {
            // ATB Calculations
            $categoryItemsATB = $ATB->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // APB Calculations
            $categoryItemsAPB = $APB->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // New direct Saldo calculations
            $categoryItemsSaldo = $SALDO->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            $sums[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => [ 
                    'hutang-unit-alat' => $categoryItemsATB->where ( 'tipe', 'hutang-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'panjar-unit-alat' => $categoryItemsATB->where ( 'tipe', 'panjar-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'mutasi-proyek'    => $categoryItemsATB->where ( 'tipe', 'mutasi-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'panjar-proyek'    => $categoryItemsATB->where ( 'tipe', 'panjar-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } )
                ],
                'apb'      => [ 
                    'hutang-unit-alat' => $categoryItemsAPB->where ( 'tipe', 'hutang-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->saldo->harga;
                    } ),
                    'panjar-unit-alat' => $categoryItemsAPB->where ( 'tipe', 'panjar-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->saldo->harga;
                    } ),
                    'mutasi-proyek'    => $categoryItemsAPB->where ( 'tipe', 'mutasi-proyek' )->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
                    {
                        return $item->quantity * $item->saldo->harga;
                    } ),
                    'panjar-proyek'    => $categoryItemsAPB->where ( 'tipe', 'panjar-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->saldo->harga;
                    } )
                ],
                'saldo'    => [ 
                    'hutang-unit-alat' => $categoryItemsSaldo->where ( 'tipe', 'hutang-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'panjar-unit-alat' => $categoryItemsSaldo->where ( 'tipe', 'panjar-unit-alat' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'mutasi-proyek'    => $categoryItemsSaldo->where ( 'tipe', 'mutasi-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } ),
                    'panjar-proyek'    => $categoryItemsSaldo->where ( 'tipe', 'panjar-proyek' )->sum ( function ($item)
                    {
                        return $item->quantity * $item->harga;
                    } )
                ]
            ];

            // Calculate totals
            $sums[ $category[ 'kode' ] ][ 'atb' ][ 'total' ]   = array_sum ( array_filter ( $sums[ $category[ 'kode' ] ][ 'atb' ], 'is_numeric' ) );
            $sums[ $category[ 'kode' ] ][ 'apb' ][ 'total' ]   = array_sum ( array_filter ( $sums[ $category[ 'kode' ] ][ 'apb' ], 'is_numeric' ) );
            $sums[ $category[ 'kode' ] ][ 'saldo' ][ 'total' ] = array_sum ( array_filter ( $sums[ $category[ 'kode' ] ][ 'saldo' ], 'is_numeric' ) );
        }

        return view ( 'dashboard.laporan.bulan_berjalan.bulan_berjalan', [ 
            'proyeks'    => $proyeks,
            'sums'       => $sums,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'headerPage' => 'Laporan Semua Proyek',
            'page'       => 'LNPB Bulan Berjalan'
        ] );
    }
}
