<?php

namespace App\Http\Controllers;

use App\Models\SPB;
use App\Models\Proyek;
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
}
