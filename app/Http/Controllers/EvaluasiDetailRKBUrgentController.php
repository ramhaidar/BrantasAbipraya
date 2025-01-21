<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Models\DetailRKBUrgent;
use App\Models\KategoriSparepart;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EvaluasiDetailRKBUrgentController extends Controller
{
    public function index ( $id )
    {
        $rkb                   = RKB::with ( [ 'proyek' ] )->find ( $id );
        $proyeks               = Proyek::orderByDesc ( 'updated_at' )->get ();
        $master_data_alat      = MasterDataAlat::all ();
        $master_data_sparepart = MasterDataSparepart::all ();
        $kategori_sparepart    = KategoriSparepart::all ();

        // Get RKB details with relationships
        $alat_detail_rkbs = RKB::where ( "tipe", "Urgent" )
            ->findOrFail ( $id )
            ->linkAlatDetailRkbs ()
            ->with ( [ 
                'masterDataAlat',
                'linkRkbDetails.detailRkbUrgent.kategoriSparepart',
                'linkRkbDetails.detailRkbUrgent.masterDataSparepart',
                'lampiranRkbUrgent',
                'timelineRkbUrgents'
            ] )
            ->orderBy ( 'id_master_data_alat' )
            ->get ();

        return view ( 'dashboard.evaluasi.urgent.detail.detail', [ 
            'rkb'                   => $rkb,
            'proyeks'               => $proyeks,
            'master_data_alat'      => $master_data_alat,
            'master_data_sparepart' => $master_data_sparepart,
            'kategori_sparepart'    => $kategori_sparepart,
            'alat_detail_rkbs'      => $alat_detail_rkbs,
            'headerPage'            => "Evaluasi Urgent",
            'page'                  => 'Detail Evaluasi Urgent',
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
            DetailRKBUrgent::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
            {
                $query->where ( 'id', $id_rkb );
            } )->update ( [ 'quantity_approved' => null ] );

            $rkb->is_evaluated = false;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
                ->with ( 'success', 'Evaluasi RKB berhasil dibatalkan!' );
        }

        // Existing evaluation logic
        $request->validate ( [ 
            'quantity_approved'   => 'required|array',
            'quantity_approved.*' => 'required|integer|min:0',
        ] );

        // Ambil data dari input
        $quantityApproved = $request->input ( 'quantity_approved' );

        // Loop untuk mengupdate setiap baris berdasarkan ID
        foreach ( $quantityApproved as $id => $quantity )
        {
            $updated = DetailRKBUrgent::where ( 'id', $id )->update ( [ 'quantity_approved' => $quantity ] );

            // Debug jika update gagal
            if ( ! $updated )
            {
                return redirect ()
                    ->back ()
                    ->with ( 'error', "Gagal mengupdate data untuk ID {$id}" );
            }
        }

        $rkb               = RKB::find ( $id_rkb );
        $rkb->is_evaluated = true;
        $rkb->save ();

        // Redirect dengan pesan sukses
        return redirect ()
            ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
            ->with ( 'success', 'RKB Berhasil di Evaluasi!' );
    }

    public function approve ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // Update all DetailRKBUrgent records for this RKB
        DetailRKBUrgent::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
        {
            $query->where ( 'id', $id_rkb );
        } )->each ( function ($detail)
        {
            $detail->incrementQuantityRemainder ( $detail->quantity_approved );
        } );

        $rkb->is_approved = true;
        $rkb->save ();

        // Redirect dengan pesan sukses
        return redirect ()
            ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
            ->with ( 'success', 'RKB Berhasil di Approve!' );
    }

    public function getDokumentasi ( $id )
    {
        $detailRkbUrgent = DetailRkbUrgent::findOrFail ( $id );

        // Assuming dokumentasi contains the folder path
        $folderPath = $detailRkbUrgent->dokumentasi;

        // Get all files from the folder
        $files = Storage::disk ( 'public' )->files ( $folderPath );

        // Prepare data for response
        $data = array_map ( function ($file)
        {
            return [ 
                'name' => basename ( $file ),
                'url'  => Storage::url ( $file ),
            ];
        }, $files );

        return response ()->json ( [ 'dokumentasi' => $data ] );
    }
}
