<?php

namespace App\Http\Controllers;

use App\Models\APB;
use App\Models\ATB;
use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LaporanLNPBTotalController extends Controller
{
    public function index ( Request $request )
    {
        $proyek = Proyek::with ( "users" )->find ( $request->id_proyek );

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "asc" )
            ->orderBy ( "id", "asc" )
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

        // === Calculate ATB, APB, and Saldo Current Period === //
        $currentDate = now ();
        $startDate   = $currentDate->copy ()->subMonth ()->day ( 26 );
        $endDate     = $currentDate->copy ()->day ( 25 );

        $ATB_Current = ATB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        $APB_Current = APB::with ( 'masterDataSparepart.KategoriSparepart' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        $SALDO_Current = Saldo::with ( 'masterDataSparepart.KategoriSparepart', 'atb' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereHas ( 'atb', function ($query) use ($startDate, $endDate)
            {
                $query->whereBetween ( 'tanggal', [ $startDate, $endDate ] );
            } )
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

        $SALDO_Before = Saldo::with ( 'masterDataSparepart.KategoriSparepart', 'atb' )
            ->where ( 'id_proyek', $request->id_proyek )
            ->whereHas ( 'atb', function ($query) use ($startDate)
            {
                $query->where ( 'tanggal', '<', $startDate );
            } )
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

            // Saldo calculations
            $categoryItemsSaldo = $SALDO_Current->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            $sums_current[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => $categoryItemsATB->sum ( function ($item)
                {
                    return $item->quantity * $item->harga;
                } ),
                'apb'      => $categoryItemsAPB->sum ( function ($item)
                {
                    return $item->quantity * $item->saldo->harga;
                } ),
                'saldo'    => $categoryItemsSaldo->sum ( function ($item)
                {
                    return $item->quantity * $item->harga;
                } )
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

            $categoryItemsSaldo_Before = $SALDO_Before->filter ( function ($item) use ($category)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode === $category[ 'kode' ];
            } );

            $sums_before[ $category[ 'kode' ] ] = [ 
                'nama'     => $category[ 'nama' ],
                'jenis'    => $category[ 'jenis' ],
                'subJenis' => $category[ 'subJenis' ],
                'atb'      => $categoryItemsATB_Before->sum ( function ($item)
                {
                    return $item->quantity * $item->harga;
                } ),
                'apb'      => $categoryItemsAPB_Before->sum ( function ($item)
                {
                    return $item->quantity * $item->saldo->harga;
                } ),
                'saldo'    => $categoryItemsSaldo_Before->sum ( function ($item)
                {
                    return $item->quantity * $item->harga;
                } )
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
}