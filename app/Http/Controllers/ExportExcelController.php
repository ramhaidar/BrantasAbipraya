<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\RKB;
use App\Models\SPB;
use Illuminate\Http\Request;
use App\Exports\RiwayatSPBExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DetailRKBUrgentExport;
use App\Exports\DetailSPBProyekExport;
use App\Exports\DetailRKBGeneralExport;
use App\Exports\ATBHutangUnitAlatExport;
use App\Exports\ATBPanjarUnitAlatProyekExport;
use App\Exports\EvaluasiDetailRKBUrgentExport;
use App\Exports\EvaluasiDetailRKBGeneralExport;
use App\Exports\ATBMutasiProyekExport;

class ExportExcelController extends Controller
{
    public function rkb_general ( Request $request )
    {
        // dd ( $request->all () );
        // Dummy function for exporting RKB General
    }

    public function detail_rkb_general ( Request $request )
    {
        // Ambil data RKB berdasarkan parameter ID
        $rkb = RKB::with ( 'proyek' )->find ( $request->id );

        if ( ! $rkb )
        {
            return redirect ()->back ()->withErrors ( [ 'error' => 'RKB tidak ditemukan' ] );
        }

        $periode  = Carbon::parse ( $rkb->periode )->locale ( 'id' )->translatedFormat ( 'F Y' );
        $fileName = "RKB General-{$rkb->nomor}-{$rkb->proyek->nama}-{$periode}.xlsx";

        // Generate dan unduh file Excel
        return Excel::download ( new DetailRKBGeneralExport( $rkb->id ), $fileName );
    }

    public function rkb_urgent ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting RKB Urgent
    }

    public function detail_rkb_urgent ( Request $request )
    {
        // Ambil data RKB berdasarkan parameter ID
        $rkb = RKB::with ( 'proyek' )->find ( $request->id );

        if ( ! $rkb )
        {
            return redirect ()->back ()->withErrors ( [ 'error' => 'RKB tidak ditemukan' ] );
        }

        $periode  = Carbon::parse ( $rkb->periode )->locale ( 'id' )->translatedFormat ( 'F Y' );
        $fileName = "RKB Urgent-{$rkb->nomor}-{$rkb->proyek->nama}-{$periode}.xlsx";

        // Generate dan unduh file Excel
        return Excel::download ( new DetailRKBUrgentExport( $rkb->id ), $fileName );
    }

    public function timeline_rkb_urgent ( Request $request )
    {
        dd ( $request->all () );

        // Dummy function for exporting Timeline RKB Urgent
    }

    public function evaluasi_rkb_general ( Request $request )
    {
        // Ambil data RKB berdasarkan parameter ID
        $rkb = RKB::with ( 'proyek' )->find ( $request->id );

        if ( ! $rkb )
        {
            return redirect ()->back ()->withErrors ( [ 'error' => 'RKB tidak ditemukan' ] );
        }

        $periode  = Carbon::parse ( $rkb->periode )->locale ( 'id' )->translatedFormat ( 'F Y' );
        $fileName = "Evaluasi RKB General-{$rkb->nomor}-{$rkb->proyek->nama}-{$periode}.xlsx";

        // Generate dan unduh file Excel
        return Excel::download ( new EvaluasiDetailRKBGeneralExport( $rkb->id ), $fileName );
    }

    public function evaluasi_rkb_urgent ( Request $request )
    {
        // Ambil data RKB berdasarkan parameter ID
        $rkb = RKB::with ( 'proyek' )->find ( $request->id );

        if ( ! $rkb )
        {
            return redirect ()->back ()->withErrors ( [ 'error' => 'RKB tidak ditemukan' ] );
        }

        $periode  = Carbon::parse ( $rkb->periode )->locale ( 'id' )->translatedFormat ( 'F Y' );
        $fileName = "Evaluasi RKB Urgent-{$rkb->nomor}-{$rkb->proyek->nama}-{$periode}.xlsx";

        // Generate dan unduh file Excel
        return Excel::download ( new EvaluasiDetailRKBUrgentExport( $rkb->id ), $fileName );
    }

    public function evaluasi_timeline_rkb_urgent ( Request $request )
    {
        dd ( $request->all () );

        // Dummy function for exporting Timeline RKB Urgent
    }

    public function spb ( Request $request )
    {
        // Ambil data SPB berdasarkan parameter ID
        $spb = SPB::with ( [ 'linkRkbSpbs.rkb.proyek' ] )->find ( $request->id );

        if ( ! $spb )
        {
            return redirect ()->back ()->withErrors ( [ 'error' => 'SPB tidak ditemukan' ] );
        }

        $fileName = "SPB-{$spb->nomor}.xlsx";

        // Generate dan unduh file Excel
        return Excel::download ( new RiwayatSPBExport( $spb->id ), $fileName );
    }

    public function spb_proyek ( Request $request )
    {
        // Get RKB data
        $rkb = RKB::with ( 'proyek' )->find ( $request->id );

        if ( ! $rkb )
        {
            return redirect ()->back ()->withErrors ( [ 'error' => 'RKB tidak ditemukan' ] );
        }

        $periode  = Carbon::parse ( $rkb->periode )->locale ( 'id' )->translatedFormat ( 'F Y' );
        $fileName = "SPB Proyek-{$rkb->nomor}-{$rkb->proyek->nama}-{$periode}.xlsx";

        // Generate and download Excel file
        return Excel::download ( new DetailSPBProyekExport( $rkb->id ), $fileName );
    }

    public function atb ( Request $request )
    {
        // Validate request
        $validated = $request->validate ( [ 
            'id'   => 'required|exists:proyek,id',
            'type' => 'required|string|in:hutang-unit-alat,panjar-unit-alat,mutasi-proyek,panjar-proyek',
        ] );

        // Get the proyek
        $proyek = \App\Models\Proyek::findOrFail ( $request->id );

        // Generate filename
        $fileName = "ATB-{$proyek->nama}-" . ucwords ( str_replace ( '-', ' ', $request->type ) ) . '.xlsx';

        // Choose export class based on type
        switch ($request->type)
        {
            case 'hutang-unit-alat':
                return Excel::download ( new ATBHutangUnitAlatExport( $request->id ), $fileName );
            case 'panjar-unit-alat':
            case 'panjar-proyek':
                return Excel::download ( new ATBPanjarUnitAlatProyekExport( $request->id, $request->type ), $fileName );
            case 'mutasi-proyek':
                return Excel::download ( new ATBMutasiProyekExport( $request->id ), $fileName );
            default:
                return redirect ()->back ()->withErrors ( [ 'error' => 'Export type not supported yet' ] );
        }
    }

    public function apb ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting APB
    }

    public function saldo ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting Saldo
    }

    public function lnpb_bulan_berjalan ( Request $request = null )
    {
        if ( $request === null )
        {
            dd ( "request is null" );
            return;
        }
        dd ( $request->all () );
        // Dummy function for exporting LNPB Bulan Berjalan
    }

    public function lnpb_total ( Request $request = null )
    {
        if ( $request === null )
        {
            dd ( "request is null" );
            return;
        }
        dd ( $request->all () );
        // Dummy function for exporting LNPB Total
    }
}
