<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use App\Http\Controllers\Controller;

class DetailSPBController extends Controller
{
    public function index ( $id )
    {
        $proyeks = Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get ();
        $rkb     = RKB::with ( [ 
            "linkAlatDetailRkbs.masterDataAlat",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart" => function ($query)
            {
                $query->orderBy ( 'nama', 'asc' );
            },
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.masterDataSparepart"  => function ($query)
            {
                $query->orderBy ( 'nama', 'asc' );
            },
            "linkAlatDetailRkbs.timelineRkbUrgents",
            "linkAlatDetailRkbs.lampiranRkbUrgent"
        ] )->find ( $id );

        $supplier = MasterDataSupplier::all ();

        return view ( 'dashboard.spb.detail.detail', [ 
            'proyeks'    => $proyeks,
            'rkb'        => $rkb,
            'supplier'   => $supplier,

            'headerPage' => "SPB",
            'page'       => 'Detail SPB',
        ] );
    }
}
