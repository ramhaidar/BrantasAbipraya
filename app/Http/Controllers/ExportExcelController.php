<?php

namespace App\Http\Controllers;
use App\Models\RKB;
use App\Exports\DetailRKBGeneralExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExportExcelController extends Controller
{
    public function rkb_general ( Request $request )
    {
        // dd ( $request->all () );
        // Dummy function for exporting RKB General
    }

    public function detail_rkb_general (Request $request)
    {
        // Ambil data RKB berdasarkan parameter ID atau data lainnya
        $rkb = RKB::find($request->id);

        if (!$rkb) {
            return redirect()->back()->withErrors(['error' => 'RKB tidak ditemukan']);
        }

        $periode = Carbon::parse($rkb->periode)->locale('id')->translatedFormat('F Y');

        $fileName = "{$rkb->nomor}-{$rkb->proyek->nama}-{$periode}.xlsx";

        // Generate dan unduh file Excel
        return Excel::download(new DetailRKBGeneralExport, $fileName);;
    }

    public function rkb_urgent ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting RKB Urgent
    }

    public function detail_rkb_urgent ()
    {
        // Dummy function for exporting Detail RKB Urgent
    }

    public function timeline_rkb_urgent ()
    {
        // Dummy function for exporting Timeline RKB Urgent
    }

    public function evaluasi_rkb_general ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting Evaluasi RKB General
    }

    public function evaluasi_rkb_urgent ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting Evaluasi RKB Urgent
    }

    public function spb ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting SPB
    }

    public function spb_proyek ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting SPB Proyek
    }

    public function atb ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting ATB
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
