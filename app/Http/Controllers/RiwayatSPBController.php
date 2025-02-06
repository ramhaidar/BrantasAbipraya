<?php

namespace App\Http\Controllers;

use App\Models\SPB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class RiwayatSPBController extends Controller
{
    public function index ( $id )
    {
        // Get single SPB record with relationships
        $spb = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataSparepart',
            'linkRkbSpbs.rkb.proyek',
            'masterDataSupplier',
        ] )->findOrFail ( $id );

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

        // Create paginator manually from single SPB with sorting
        $TableData = new LengthAwarePaginator(
            collect ( [ $spb ] )
                ->sortByDesc ( 'updated_at' )
                ->sortByDesc ( 'id' ),  // items sorted
            1,                // total
            1,                // per page
            1                 // current page
        );

        return view ( 'dashboard.spb.riwayat.riwayat', [ 
            'proyeks'    => $proyeks,
            'TableData'  => $TableData,
            'headerPage' => "SPB Supplier",
            'page'       => 'Riwayat SPB Supplier [' . $spb->linkRkbSpbs[ 0 ]->rkb->proyek->nama . ' | ' . $spb->linkRkbSpbs[ 0 ]->rkb->nomor . ']',
        ] );
    }

    // Fungsi untuk mengekspor data SPB menjadi PDF
    public function exportPDF ( $id )
    {
        $spb = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataSparepart',
            'linkRkbSpbs',
            'masterDataSupplier',
        ] )->findOrFail ( $id );

        $totalHarga       = 0;
        $totalJumlahHarga = 0;

        foreach ( $spb->linkSpbDetailSpb as $item )
        {
            $totalHarga += $item->detailSpb->harga;
            $totalJumlahHarga += $item->detailSpb->quantity_po_po * $item->detailSpb->harga;
        }

        $ppn        = $totalJumlahHarga * 0.11;
        $grandTotal = $totalJumlahHarga + $ppn;

        $pdf = Pdf::loadView ( 'dashboard.spb.riwayat.partials.export-pdf', compact (
            'spb',
            'totalHarga',
            'totalJumlahHarga',
            'ppn',
            'grandTotal'
        ) );

        return $pdf->stream ( 'surat_pemesanan_barang.pdf' );
    }
}
