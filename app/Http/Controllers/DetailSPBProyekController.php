<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\SPB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use App\Http\Controllers\Controller;

class DetailSPBProyekController extends Controller
{
    public function index ( $id )
    {
        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();
        $rkb     = RKB::with ( [ 
            "linkAlatDetailRkbs.masterDataAlat",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart" => fn ( $query ) => $query->orderBy ( 'nama' ),
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.masterDataSparepart"  => fn ( $query ) => $query->orderBy ( 'nama' ),
            "linkAlatDetailRkbs.timelineRkbUrgents",
            "linkAlatDetailRkbs.lampiranRkbUrgent",
        ] )->findOrFail ( $id );

        $totalItems = $rkb->linkAlatDetailRkbs->sum ( function ($detail1)
        {
            return $detail1->linkRkbDetails->sum ( function ($detail2)
            {
                $remainder = $detail2->detailRkbUrgent?->quantity_remainder ??
                    $detail2->detailRkbGeneral?->quantity_remainder ?? 0;
                return $remainder > 0 ? 1 : 0;
            } );
        } );

        $riwayatSpbs = SPB::with (
            [ 'linkSpbDetailSpb.detailSpb' ]
        )
            ->where ( 'is_addendum', false )
            ->whereIn ( 'id', $rkb->spbs ()->pluck ( 'id_spb' ) )
            ->get ();

        return view ( 'dashboard.spb.proyek.detail.detail', [ 
            'proyeks'     => $proyeks,
            'rkb'         => $rkb,
            'supplier'    => MasterDataSupplier::all (),
            'totalItems'  => $totalItems,
            'riwayatSpbs' => $riwayatSpbs,

            'headerPage'  => "SPB Proyek",
            'page'        => 'Detail SPB Proyek',
        ] );
    }
}
