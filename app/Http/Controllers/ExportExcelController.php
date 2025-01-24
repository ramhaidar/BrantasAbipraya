<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExportExcelController extends Controller
{
    public function rkb_general ( Request $request )
    {
        dd ( $request->all () );
        // Dummy function for exporting RKB General
    }

    public function detail_rkb_general ()
    {
        // Dummy function for exporting Detail RKB General
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
