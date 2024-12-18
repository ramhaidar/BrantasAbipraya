<?php

namespace App\Http\Controllers;

use App\Models\SPB;
use App\Models\Proyek;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RiwayatSPBController extends Controller
{
    public function index ( $id )
    {
        $proyeks = Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get ();
        $spb     = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.sparepart',
            'linkRkbSpbs',
            'supplier',
        ] )->findOrFail ( $id );

        return view ( 'dashboard.spb.riwayat.riwayat', [ 
            'proyeks'    => $proyeks,
            'spb'        => $spb,

            'headerPage' => "SPB",
            'page'       => 'Riwayat SPB',
        ] );
    }

    public function getRiwayatFromSPB ( $id )
    {
    }

    // Fungsi untuk mengekspor data SPB menjadi PDF
    public function exportPDF($id)
    {
        $spb = SPB::with([
            'linkSpbDetailSpb.detailSpb.sparepart',
            'linkRkbSpbs',
            'supplier',
        ])->findOrFail($id);

        $totalHarga = 0;
        $totalJumlahHarga = 0;

        foreach ($spb->linkSpbDetailSpb as $item) {
            $totalHarga += $item->detailSpb->harga;
            $totalJumlahHarga += $item->detailSpb->quantity * $item->detailSpb->harga;
        }

        $ppn = $totalJumlahHarga * 0.11;
        $grandTotal = $totalJumlahHarga + $ppn;

        $pdf = PDF::loadView('dashboard.spb.riwayat.export-pdf', compact(
            'spb',
            'totalHarga',
            'totalJumlahHarga',
            'ppn',
            'grandTotal'
        ));

        return $pdf->stream('surat_pemesanan_barang.pdf');
    }
}
