<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\SaldoProyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Models\DetailRKBGeneral;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;

class EvaluasiDetailRKBGeneralController extends Controller
{
    public function index ( $id )
    {
        $rkb                   = RKB::with ( [ 'proyek' ] )->find ( $id );
        $proyeks               = Proyek::orderByDesc ( 'updated_at' )->get ();
        $master_data_alat      = MasterDataAlat::all ();
        $master_data_sparepart = MasterDataSparepart::all ();
        $kategori_sparepart    = KategoriSparepart::all ();

        // Get RKB details with relationships
        $alat_detail_rkbs = RKB::where ( "tipe", "General" )
            ->findOrFail ( $id )
            ->linkAlatDetailRkbs ()
            ->with ( [ 
                'masterDataAlat',
                'linkRkbDetails.detailRkbGeneral.kategoriSparepart',
                'linkRkbDetails.detailRkbGeneral.masterDataSparepart',
            ] )
            ->orderBy ( 'id_master_data_alat' )
            ->get ();

        // Get stock quantities for each sparepart in this project using existing saldos relationship
        $stockQuantities = Saldo::where ( 'id_proyek', $rkb->id_proyek )
            ->get ()
            ->groupBy ( 'id_master_data_sparepart' )
            ->map ( function ($items)
            {
                return $items->sum ( 'quantity' );
            } );

        // dd ( $rkb );

        return view ( 'dashboard.evaluasi.general.detail.detail', [ 
            'rkb'                   => $rkb,
            'proyeks'               => $proyeks,
            'master_data_alat'      => $master_data_alat,
            'master_data_sparepart' => $master_data_sparepart,
            'kategori_sparepart'    => $kategori_sparepart,
            'alat_detail_rkbs'      => $alat_detail_rkbs,
            'headerPage'            => "Evaluasi General",
            'page'                  => 'Detail Evaluasi General',
            'stockQuantities'       => $stockQuantities,
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

    public function approveVP ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // Check if can be approved by VP
        if ( ! $rkb->is_evaluated )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB harus dievaluasi terlebih dahulu!' );
        }

        if ( $rkb->is_approved_vp )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB sudah di-approve oleh VP!' );
        }

        $rkb->is_approved_vp = true;
        $rkb->vp_approved_at = now ();
        $rkb->save ();

        return redirect ()
            ->route ( 'evaluasi_rkb_general.detail.index', $id_rkb )
            ->with ( 'success', 'RKB Berhasil di Approve oleh VP!' );
    }

    public function approveSVP ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // Check if can be approved by SVP
        if ( ! $rkb->is_approved_vp )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB harus di-approve oleh VP terlebih dahulu!' );
        }

        if ( $rkb->is_approved_svp )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB sudah di-approve oleh SVP!' );
        }

        // Update all DetailRKBGeneral records for this RKB
        DetailRKBGeneral::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
        {
            $query->where ( 'id', $id_rkb );
        } )->each ( function ($detail)
        {
            $detail->incrementQuantityRemainder ( $detail->quantity_approved );
        } );

        $rkb->is_approved_svp = true;
        $rkb->svp_approved_at = now ();
        $rkb->save ();

        return redirect ()
            ->route ( 'evaluasi_rkb_general.detail.index', $id_rkb )
            ->with ( 'success', 'RKB Berhasil di Approve oleh SVP!' );
    }
}
