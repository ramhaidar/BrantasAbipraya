<?php

namespace App\Http\Controllers;

use App\Models\SPB;
use App\Models\Proyek;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class RiwayatSPBController extends Controller
{
    public function index ( $id )
    {
        $user = auth ()->user ();

        // Get single SPB record with relationships
        $spb = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataSparepart',
            'linkRkbSpbs.rkb.proyek',
            'masterDataSupplier',
        ] )->findOrFail ( $id );

        // Create paginator manually from single SPB
        $TableData = new LengthAwarePaginator(
            collect ( [ $spb ] ),  // items
            1,                // total
            1,                // per page
            1                 // current page
        );

        // Get projects for selection
        $proyeks = [];
        if ( $user->role !== 'Pegawai' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "updated_at", "desc" )
                ->orderBy ( "id", "desc" )
                ->get ();
        }

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
