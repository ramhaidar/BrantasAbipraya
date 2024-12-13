<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DetailSPBController extends Controller
{
    public function index ( $id )
    {
        $proyeks = Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get ();
        $rkb     = RKB::with (
            "linkAlatDetailRkbs.masterDataAlat",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.masterDataSparepart",
            "linkAlatDetailRkbs.timelineRkbUrgents",
            "linkAlatDetailRkbs.lampiranRkbUrgent",
        )->find ( $id );

        return view ( 'dashboard.spb.detail.detail', [ 
            'proyeks'    => $proyeks,
            'rkb'        => $rkb,

            'headerPage' => "SPB",
            'page'       => 'Data SPB',
        ] );
    }
}
