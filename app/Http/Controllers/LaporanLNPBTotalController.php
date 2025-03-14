<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\APB;
use App\Models\ATB;
use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LaporanLNPBTotalController extends Controller
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

            // Special case handling for November-December period
            if ( $startDate->month == 11 && $endDate->month == 12 )
            {
                $startDate = $startDate->copy ()->setDay ( 26 ); // November 26th
                $endDate   = $endDate->copy ()->endOfMonth ();   // December 31st
            }
            // Special case handling for December-January period
            else if ( $startDate->month == 12 && $endDate->month == 1 )
            {
                // For December-January, we only want January 1st to January 25th
                $startDate = Carbon::parse ( $endDate->year . '-01-01' ); // January 1st
                $endDate   = Carbon::parse ( $endDate->year . '-01-25' ); // January 25th
            }
            // Normal case: Ensure startDate is on the 26th and endDate is on the 25th
            else
            {
                $startDate = $startDate->day ( 26 );
                $endDate   = $endDate->day ( 25 );
            }
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

        // === Calculate ATB, APB, and Saldo Current Period === //
        $ATB_Current = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        $APB_Current = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        // +++ Calculate ATB, APB, and Saldo Before Current Period +++
        $ATB_Before = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->where ( 'tanggal', '<', $startDate )
            ->get ();

        $APB_Before = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->where ( 'tanggal', '<', $startDate )
            ->get ();
        // +++

        $sums_current = [];
        $sums_before  = []; // +++

        foreach ( $data as $category )
        {
            // ATB Calculations
            $categoryItemsATB = $ATB_Current->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // APB Calculations
            $categoryItemsAPB = $APB_Current->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // Calculate ATB Value
            $atbValue = $categoryItemsATB->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            // Calculate APB Value (only accepted items)
            $apbValue = $categoryItemsAPB->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * $item->saldo->harga;
            } );

            // Calculate Saldo as ATB - APB
            $saldoValue = $atbValue - $apbValue;

            $sums_current[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => $atbValue,
                'apb'      => $apbValue,
                'saldo'    => $saldoValue
            ];

            // +++ Calculate sums_before +++
            $categoryItemsATB_Before = $ATB_Before->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            $categoryItemsAPB_Before = $APB_Before->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // Calculate ATB Value for before period
            $atbValueBefore = $categoryItemsATB_Before->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            // Calculate APB Value for before period (only accepted items)
            $apbValueBefore = $categoryItemsAPB_Before->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * $item->saldo->harga;
            } );

            // Calculate Saldo as ATB - APB for before period
            $saldoValueBefore = $atbValueBefore - $apbValueBefore;

            $sums_before[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => $atbValueBefore,
                'apb'      => $apbValueBefore,
                'saldo'    => $saldoValueBefore
            ];
            // +++
        }

        // dd ( $sums_current, $sums_before );

        return view ( 'dashboard.laporan.total.total', [ 
            'proyek'       => $proyek,
            'proyeks'      => $proyeks,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'sums_current' => $sums_current,
            'sums_before'  => $sums_before,
            'headerPage'   => $proyek->nama,
            'page'         => 'LNPB Total',
        ] );
    }

    public function semuaTotal_index ( Request $request )
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

            // Special case handling for November-December period
            if ( $startDate->month == 11 && $endDate->month == 12 )
            {
                $startDate = $startDate->copy ()->setDay ( 26 ); // November 26th
                $endDate   = $endDate->copy ()->endOfMonth ();   // December 31st
            }
            // Special case handling for December-January period
            else if ( $startDate->month == 12 && $endDate->month == 1 )
            {
                // For December-January, we only want January 1st to January 25th
                $startDate = Carbon::parse ( $endDate->year . '-01-01' ); // January 1st
                $endDate   = Carbon::parse ( $endDate->year . '-01-25' ); // January 25th
            }
            // Normal case: Ensure startDate is on the 26th and endDate is on the 25th
            else
            {
                $startDate = $startDate->day ( 26 );
                $endDate   = $endDate->day ( 25 );
            }
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

        // === Calculate ATB, APB, and Saldo Current Period === //
        $ATB_Current = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        $APB_Current = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        // +++ Calculate ATB, APB, and Saldo Before Current Period +++
        $ATB_Before = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'tanggal', '<', $startDate )
            ->get ();

        $APB_Before = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'tanggal', '<', $startDate )
            ->get ();
        // +++

        $sums_current = [];
        $sums_before  = []; // +++

        foreach ( $data as $category )
        {
            // ATB Calculations
            $categoryItemsATB = $ATB_Current->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // APB Calculations
            $categoryItemsAPB = $APB_Current->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // Calculate ATB Value
            $atbValue = $categoryItemsATB->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            // Calculate APB Value (only accepted items)
            $apbValue = $categoryItemsAPB->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * $item->saldo->harga;
            } );

            // Calculate Saldo as ATB - APB
            $saldoValue = $atbValue - $apbValue;

            $sums_current[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => $atbValue,
                'apb'      => $apbValue,
                'saldo'    => $saldoValue
            ];

            // +++ Calculate sums_before +++
            $categoryItemsATB_Before = $ATB_Before->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            $categoryItemsAPB_Before = $APB_Before->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            // Calculate ATB Value for before period
            $atbValueBefore = $categoryItemsATB_Before->sum ( function ($item)
            {
                return $item->quantity * $item->harga;
            } );

            // Calculate APB Value for before period (only accepted items)
            $apbValueBefore = $categoryItemsAPB_Before->whereNotIn ( 'status', [ 'pending', 'rejected' ] )->sum ( function ($item)
            {
                return $item->quantity * $item->saldo->harga;
            } );

            // Calculate Saldo as ATB - APB for before period
            $saldoValueBefore = $atbValueBefore - $apbValueBefore;

            $sums_before[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => $atbValueBefore,
                'apb'      => $apbValueBefore,
                'saldo'    => $saldoValueBefore
            ];
            // +++
        }

        // dd ( $sums_current, $sums_before );

        return view ( 'dashboard.laporan.total.total', [ 
            'proyeks'      => $proyeks,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'sums_current' => $sums_current,
            'sums_before'  => $sums_before,
            'headerPage'   => 'Laporan Semua Proyek',
            'page'         => 'LNPB Total',
        ] );
    }
}