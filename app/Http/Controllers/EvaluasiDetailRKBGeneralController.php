<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DetailRKBGeneral;
use App\Models\KategoriSparepart;
use App\Models\MasterDataAlat;
use App\Models\MasterDataSparepart;
use App\Models\Proyek;
use App\Models\RKB;
use Illuminate\Http\Request;

class EvaluasiDetailRKBGeneralController extends Controller
{
    public function index ( $id )
    {
        $rkb                   = RKB::with ( [ "proyek" ] )->find ( $id );
        $proyeks               = Proyek::orderByDesc ( "updated_at" )->get ();
        $master_data_alat      = MasterDataAlat::all ();
        $master_data_sparepart = MasterDataSparepart::all ();
        $kategori_sparepart    = KategoriSparepart::all ();

        $details = DetailRKBGeneral::query ()
            ->select ( [ 
                "detail_rkb_general.*",
                "kategori_sparepart.kode as kategori_sparepart_kode",
                "kategori_sparepart.nama as kategori_sparepart_nama",
                "master_data_sparepart.nama as sparepart_nama",
                "master_data_sparepart.part_number",
                "master_data_sparepart.merk",
            ] )
            ->leftJoin ( "kategori_sparepart", "detail_rkb_general.id_kategori_sparepart_sparepart", "=", "kategori_sparepart.id" )
            ->leftJoin ( "master_data_sparepart", "detail_rkb_general.id_master_data_sparepart", "=", "master_data_sparepart.id" )
            ->whereHas ( "linkRkbDetails.linkAlatDetailRkb.rkb", function ($q) use ($id)
            {
                $q->where ( "id", $id );
            } )
            ->orderBy ( "master_data_sparepart.part_number", "asc" )
            ->paginate ( 100 );

        // Transform the data with original names
        $details->getCollection ()->transform ( function ($item)
        {
            $alat = optional ( optional ( $item->linkRkbDetails->first () )->linkAlatDetailRkb->masterDataAlat );

            return (object) [ 
                'id'                 => $item->id,
                'master_data_alat'   => $alat->jenis_alat ?? "-",
                'kode_alat'          => $alat->kode_alat ?? "-",
                'kategori_sparepart' => ( $item->kategori_sparepart_kode ? "{$item->kategori_sparepart_kode}: " : "" ) . ( $item->kategori_sparepart_nama ?? "-" ),
                'sparepart_nama'     => $item->sparepart_nama ?? "-",
                'part_number'        => $item->part_number ?? "-",
                'merk'               => $item->merk ?? "-",
                'quantity_requested' => $item->quantity_requested,
                'quantity_approved'  => $item->quantity_approved,
                'quantity_in_stock'  => $item->quantity_in_stock ?? 0,
                'satuan'             => $item->satuan
            ];
        } );

        return view ( "dashboard.evaluasi.general.detail.detail", [ 
            "rkb"                   => $rkb,
            "details"               => $details,
            "proyeks"               => $proyeks,
            "master_data_alat"      => $master_data_alat,
            "master_data_sparepart" => $master_data_sparepart,
            "kategori_sparepart"    => $kategori_sparepart,
            "headerPage"            => "Evaluasi General",
            "page"                  => "Detail Evaluasi General",
        ] );
    }

    public function evaluate ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // If already evaluated, cancel evaluation
        if ( $rkb->is_evaluated )
        {
            // Cannot cancel if already approved
            if ( $rkb->is_approved )
            {
                return redirect ()
                    ->back ()
                    ->with ( 'error', 'Tidak dapat membatalkan evaluasi RKB yang sudah di-approve!' );
            }

            // Reset all quantity_approved values to 0
            DetailRKBGeneral::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
            {
                $query->where ( 'id', $id_rkb );
            } )->update ( [ 'quantity_approved' => null ] );

            $rkb->is_evaluated = false;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_general.detail.index', $id_rkb )
                ->with ( 'success', 'Evaluasi RKB berhasil dibatalkan!' );
        }

        // Existing evaluation logic
        $request->validate ( [ 
            "quantity_approved"   => "required|array",
            "quantity_approved.*" => "required|integer|min:0",
        ] );

        // Ambil data dari input
        $quantityApproved = $request->input ( "quantity_approved" );

        // Loop untuk mengupdate setiap baris berdasarkan ID
        foreach ( $quantityApproved as $id => $quantity )
        {
            $updated = DetailRKBGeneral::where ( "id", $id )->update ( [ 
                "quantity_approved" => $quantity,
            ] );

            // Debug jika update gagal
            if ( ! $updated )
            {
                return redirect ()
                    ->back ()
                    ->with ( "error", "Gagal mengupdate data untuk ID {$id}" );
            }
        }

        $rkb               = RKB::find ( $id_rkb );
        $rkb->is_evaluated = true;
        $rkb->save ();

        // Redirect dengan pesan sukses
        return redirect ()
            ->route ( "evaluasi_rkb_general.detail.index", $id_rkb )
            ->with ( "success", "RKB Berhasil di Evaluasi!" );
    }

    public function approve ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // Update all DetailRKBGeneral records for this RKB
        DetailRKBGeneral::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
        {
            $query->where ( 'id', $id_rkb );
        } )->each ( function ($detail)
        {
            $detail->incrementQuantityRemainder ( $detail->quantity_approved );
        } );

        $rkb->is_approved = true;
        $rkb->save ();

        return redirect ()
            ->route ( 'evaluasi_rkb_general.detail.index', $id_rkb )
            ->with ( 'success', 'RKB Berhasil di Approve!' );
    }
}
