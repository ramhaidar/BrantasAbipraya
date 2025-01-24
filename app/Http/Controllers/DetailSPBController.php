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

        $riwayatSpb = SPB::with ( 'linkSpbDetailSpb' )
            ->whereIn ( 'id', $rkb->spbs ()->pluck ( 'id_spb' ) )
            ->get ();

        $spbAddendumEd = SPB::whereIn ( 'id', $rkb->spbs ()->pluck ( 'id_spb' ) )
            ->where ( 'is_addendum', true )
            ->where ( function ($query)
            {
                $query->where ( 'nomor', 'not like', '%-1' )
                    ->where ( 'nomor', 'not like', '%-2' )
                    ->where ( 'nomor', 'not like', '%-3' )
                    ->where ( 'nomor', 'not like', '%-4' )
                    ->where ( 'nomor', 'not like', '%-5' )
                    ->where ( 'nomor', 'not like', '%-6' )
                    ->where ( 'nomor', 'not like', '%-7' )
                    ->where ( 'nomor', 'not like', '%-8' )
                    ->where ( 'nomor', 'not like', '%-9' );
            } )
            ->get ();  // Changed from first() to get()

        return view ( 'dashboard.spb.detail.detail', [ 
            'proyeks'       => $proyeks,
            'rkb'           => $rkb,
            'supplier'      => MasterDataSupplier::all (),
            'totalItems'    => $totalItems,
            'riwayatSpb'    => $riwayatSpb,
            'spbAddendumEd' => $spbAddendumEd,

            'headerPage'    => "SPB Supplier",
            'page'          => 'Detail SPB Supplier' . ' [' . $rkb->proyek->nama . ' | ' . $rkb->nomor . ' ]',
        ] );
    }

    public function getSparepart ( $idSupplier )
    {
        try
        {
            $supplier = MasterDataSupplier::with ( 'masterDataSpareparts' )->find ( $idSupplier );

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
            'id_rkb'               => 'required|exists:rkb,id',
            'supplier_main'        => 'required|exists:master_data_supplier,id',
            'sparepart'            => 'required|array',
            'sparepart.*'          => 'required|exists:master_data_sparepart,id',
            'qty'                  => 'required|array',
            'qty.*'                => 'required|numeric|min:0',
            'harga'                => 'required|array',
            'harga.*'              => 'required|string',
            'satuan'               => 'required|array',
            'satuan.*'             => 'required|string',
            'alat_detail_id'       => 'required|array',
            'alat_detail_id.*'     => 'required|exists:link_alat_detail_rkb,id',
            'link_rkb_detail_id'   => 'required|array',
            'link_rkb_detail_id.*' => 'required|exists:link_rkb_detail,id',

            'spb_addendum_id'      => [ 'string', 'nullable' ],
        ] );

        \DB::beginTransaction ();

        try
        {
            if ( $validated[ 'spb_addendum_id' ] == null )
            {
                // Create new SPB
                $spb = SPB::create ( [ 
                    'nomor'                   => 'SPB-' . now ()->format ( 'YmdHis' ),
                    'is_addendum'             => false,
                    'id_master_data_supplier' => $validated[ 'supplier_main' ],
                    'tanggal'                 => now (),
                ] );

                $message = "SPB berhasil dibuat";
            }
            else
            {
                $originalSpb = SPB::findOrFail ( $validated[ 'spb_addendum_id' ] );

                // Get base SPB number
                $baseNumber = $originalSpb->nomor;

                // Find the highest increment for this base number
                $highestIncrement = SPB::where ( 'nomor', 'LIKE', $baseNumber . '-%' )
                    ->get ()
                    ->map ( function ($item) use ($baseNumber)
                    {
                        if ( preg_match ( '/-(\d+)$/', $item->nomor, $matches ) )
                        {
                            return (int) $matches[ 1 ];
                        }
                        return 0;
                    } )
                    ->max ();

                $nomorSPB = $baseNumber . '-' . ( $highestIncrement + 1 );

                // Create new SPB
                $spb = SPB::create ( [ 
                    'nomor'                   => $nomorSPB,
                    'is_addendum'             => false,
                    'id_master_data_supplier' => $validated[ 'supplier_main' ],
                    'tanggal'                 => now (),
                    'id_spb_original'         => $originalSpb->id,
                ] );

                $message = "SPB berhasil di Addendum";
            }

            // Use the corrected relationship name
            $linkRKBSPB = $spb->linkRkbSpbs ()->create ( [ 
                'id_rkb' => $validated[ 'id_rkb' ],
                'id_spb' => $spb->id
            ] );

            // Loop through each sparepart item
            foreach ( $validated[ 'sparepart' ] as $index => $sparepartId )
            {
                $qty             = $validated[ 'qty' ][ $index ];
                $satuan          = $validated[ 'satuan' ][ $index ];
                $alatDetailId    = $validated[ 'alat_detail_id' ][ $index ];
                $linkRkbDetailId = $validated[ 'link_rkb_detail_id' ][ $index ];

                // Clean and extract numeric value from price string
                $harga = preg_replace ( '/[^0-9]/', '', $validated[ 'harga' ][ $index ] );

                // Skip if quantity is 0
                if ( $qty <= 0 ) continue;

                // Create DetailSPB with cleaned price and satuan
                $detailSPB = DetailSPB::create ( [ 
                    'quantity_po'              => $qty,
                    'harga'                    => (int) $harga,
                    'satuan'                   => $satuan,
                    'id_master_data_sparepart' => $sparepartId,
                    'id_master_data_alat'      => $alatDetailId,
                    'id_link_rkb_detail'       => $linkRkbDetailId,
                ] );

                // Create LinkSPBDetailSPB
                $spb->linkSpbDetailSpb ()->create ( [ 
                    'id_detail_spb' => $detailSPB->id
                ] );

                // Update quantity remainder in RKB detail using the correct link_rkb_detail_id
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

                $detail->decrementQuantityRemainder ( $qty );
            }

            \DB::commit ();
            return redirect ()->back ()->with ( 'success', $message );
        }
        catch ( \Exception $e )
        {
            \DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal membuat SPB: ' . $e->getMessage () );
        }
    }
}
