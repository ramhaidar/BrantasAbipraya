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
        $rkb                   = RKB::with ( [ 'proyek' ] )->find ( $id );
        $proyeks               = Proyek::orderByDesc ( 'updated_at' )->get ();
        $master_data_alat      = MasterDataAlat::all ();
        $master_data_sparepart = MasterDataSparepart::all ();
        $kategori_sparepart    = KategoriSparepart::all ();

                "master_data_sparepart.merk",
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

        return view ( 'dashboard.evaluasi.general.detail.detail', [ 
            'rkb'                   => $rkb,
            'proyeks'               => $proyeks,
            'master_data_alat'      => $master_data_alat,
            'master_data_sparepart' => $master_data_sparepart,
            'kategori_sparepart'    => $kategori_sparepart,
            'alat_detail_rkbs'      => $alat_detail_rkbs,
            'headerPage'            => "Evaluasi General",
            'page'                  => 'Detail Evaluasi General',
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
