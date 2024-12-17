<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\SPB;
use App\Models\Proyek;
use App\Models\DetailSPB;
use Illuminate\Http\Request;
use App\Models\LinkRKBDetail;
use App\Models\LinkSPBDetailSPB;
use App\Models\LinkAlatDetailRKB;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;

class DetailSPBController extends Controller
{
    public function index ( $id )
    {
        $proyeks = Proyek::with ( "users" )->latest ( "updated_at" )->get ();
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

        $riwayatSpb = SPB::with ( 'linkSpbDetailSpb' )
            ->whereIn ( 'id', $rkb->spbs ()->pluck ( 'id_spb' ) )
            ->get ();

        return view ( 'dashboard.spb.detail.detail', [ 
            'proyeks'    => $proyeks,
            'rkb'        => $rkb,
            'supplier'   => MasterDataSupplier::all (),
            'totalItems' => $totalItems,
            'riwayatSpb' => $riwayatSpb,
            'headerPage' => "SPB",
            'page'       => 'Detail SPB',
        ] );
    }

    public function getSparepart ( $idSupplier )
    {
        try
        {
            $supplier = MasterDataSupplier::with ( 'spareparts' )->find ( $idSupplier );

            return response ()->json ( $supplier );
        }
        catch ( \Exception $e )
        {
            return response ()->json ( [] );
        }
    }

    public function store ( Request $request )
    {
        // Validate request
        $validated = $request->validate ( [ 
            'id_rkb'        => 'required|exists:rkb,id',
            'supplier_main' => 'required|exists:master_data_supplier,id',
            'sparepart'     => 'required|array',
            'sparepart.*'   => 'required|exists:master_data_sparepart,id',
            'qty'           => 'required|array',
            'qty.*'         => 'required|numeric|min:0',
            'harga'         => 'required|array',
            'harga.*'       => 'required|string',
            'satuan'        => 'required|array',
            'satuan.*'      => 'required|string',
        ] );

        \DB::beginTransaction ();

        try
        {
            // Create new SPB
            $spb = SPB::create ( [ 
                'nomor'                   => 'SPB-' . now ()->format ( 'YmdHis' ),
                'id_master_data_supplier' => $validated[ 'supplier_main' ],
                'tanggal'                 => now (),
            ] );

            // Use the corrected relationship name
            $linkRKBSPB = $spb->linkRkbSpbs ()->create ( [ 
                'id_rkb' => $validated[ 'id_rkb' ],
                'id_spb' => $spb->id
            ] );

            // Loop through each sparepart item
            foreach ( $validated[ 'sparepart' ] as $linkRkbDetailId => $sparepartId )
            {
                $qty    = $validated[ 'qty' ][ $linkRkbDetailId ];
                $satuan = $validated[ 'satuan' ][ $linkRkbDetailId ];

                // Clean and extract numeric value from price string
                $harga = preg_replace ( '/[^0-9]/', '', $validated[ 'harga' ][ $linkRkbDetailId ] );

                // Skip if quantity is 0
                if ( $qty <= 0 ) continue;

                // Create DetailSPB with cleaned price and satuan
                $detailSPB = DetailSPB::create ( [ 
                    'quantity'                 => $qty,
                    'harga'                    => (int) $harga,
                    'satuan'                   => $satuan,
                    'id_master_data_sparepart' => $sparepartId
                ] );

                // Create LinkSPBDetailSPB
                $spb->linkSpbDetailSpb ()->create ( [ 
                    'id_detail_spb' => $detailSPB->id
                ] );

                // Update quantity remainder in RKB detail
                $linkRKBDetail = LinkRKBDetail::findOrFail ( $linkRkbDetailId );
                $rkb           = RKB::findOrFail ( $validated[ 'id_rkb' ] );

                if ( $rkb->tipe === 'urgent' )
                {
                    $detail = $linkRKBDetail->detailRkbUrgent;
                }
                else
                {
                    $detail = $linkRKBDetail->detailRkbGeneral;
                }

                if ( ! $detail || $detail->quantity_remainder < $qty )
                {
                    throw new \Exception( "Quantity tidak valid untuk sparepart yang dipilih" );
                }

                $detail->decrement ( 'quantity_remainder', $qty );
            }

            \DB::commit ();
            return redirect ()->back ()->with ( 'success', 'SPB berhasil dibuat' );

        }
        catch ( \Exception $e )
        {
            \DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal membuat SPB: ' . $e->getMessage () );
        }
    }
}
