<?php

namespace App\Http\Controllers;

use App\Models\SPB;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use App\Http\Controllers\Controller;

class DetailSPBProyekController extends Controller
{
    public function index ( Request $request, $id )
    {
        $rkb = RKB::findOrFail ( $id );

        $spbs = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataAlat',
            'linkSpbDetailSpb.detailSpb.masterDataSparepart.kategoriSparepart',
            'linkSpbDetailSpb.detailSpb.atbs',
            'masterDataSupplier',
        ] )
            ->where ( 'is_addendum', false )
            ->whereIn ( 'id', $rkb->spbs ()->pluck ( 'id_spb' ) )
            ->get ();

        // Create a simple paginator from collection with 10 items per page
        $TableData = new \Illuminate\Pagination\LengthAwarePaginator(
            $spbs->forPage ( $request->get ( 'page', 1 ), 10 ),
            $spbs->count (),
            10,
            $request->get ( 'page', 1 ),
            [ 'path' => $request->url (), 'query' => $request->query () ]
        );

        $proyeks = auth ()->user ()->role !== 'Pegawai'
            ? Proyek::with ( "users" )->latest ( "updated_at" )->latest ( "id" )->get ()
            : [];

        $TableData->sortByDesc ( 'updated_at' )
            ->sortByDesc ( 'id' );

        return view ( 'dashboard.spb.proyek.detail.detail', [ 
            'headerPage' => "SPB Proyek",
            'page'       => "Detail SPB Proyek [{$rkb->proyek->nama} | {$rkb->nomor}]",
            'proyeks'    => $proyeks,
            'TableData'  => $TableData,
            'rkb'        => $rkb,
            'supplier'   => MasterDataSupplier::all (),
        ] );
    }
}
