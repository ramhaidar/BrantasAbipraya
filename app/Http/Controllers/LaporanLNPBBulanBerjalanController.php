<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;

class LaporanLNPBBulanBerjalanController extends Controller
{
    public function index ( Request $request )
    {
        $proyek = Proyek::with ( "users" )->find ( $request->id_proyek );

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "asc" )
            ->orderBy ( "id", "asc" )
            ->get ();

        return view ( 'dashboard.laporan.bulan_berjalan.bulan_berjalan', [ 
            'proyek'    => $proyek,
            'proyeks'    => $proyeks,

            "headerPage" => $proyek->nama,
            'page'       => 'LNPB Bulan Berjalan',
        ] );
    }
}
