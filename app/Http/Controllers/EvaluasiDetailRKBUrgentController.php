<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Saldo;
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
    public function index ( Request $request, $id )
    {
        $allowedPerPage = [ 10, 25, 50, 100, -1 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $rkb = RKB::with ( [ 'proyek' ] )->find ( $id );

        // Get RKB details with relationships and ordering
        $query = DetailRKBUrgent::with([
            'linkRkbDetails.linkAlatDetailRkb.masterDataAlat',
            'linkRkbDetails.linkAlatDetailRkb.timelineRkbUrgents',
            'linkRkbDetails.linkAlatDetailRkb.lampiranRkbUrgent',
            'kategoriSparepart',
            'masterDataSparepart'
        ])
            ->leftJoin('master_data_sparepart', 'detail_rkb_urgent.id_master_data_sparepart', '=', 'master_data_sparepart.id')
            ->whereHas('linkRkbDetails.linkAlatDetailRkb', function($query) use ($id) {
                $query->where('id_rkb', $id);
            })
            ->select([
                'detail_rkb_urgent.*',
                'master_data_sparepart.part_number'
            ])
            ->orderByRaw('CAST(master_data_sparepart.part_number AS CHAR) DESC');

        // Add search functionality
        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'satuan', 'like', "%{$search}%" )
                    ->orWhere ( 'nama_koordinator', 'like', "%{$search}%" )
                    ->orWhereHas ( 'masterDataSparepart', function ($q) use ($search)
                    {
                        $q->where ( 'nama', 'like', "%{$search}%" )
                            ->orWhere ( 'part_number', 'like', "%{$search}%" )
                            ->orWhere ( 'merk', 'like', "%{$search}%" );
                    } )
                    ->orWhereHas ( 'kategoriSparepart', function ($q) use ($search)
                    {
                        $q->where ( 'kode', 'like', "%{$search}%" )
                            ->orWhere ( 'nama', 'like', "%{$search}%" );
                    } )
                    ->orWhereHas ( 'linkRkbDetails.linkAlatDetailRkb.masterDataAlat', function ($q) use ($search)
                    {
                        $q->where ( 'jenis_alat', 'like', "%{$search}%" )
                            ->orWhere ( 'kode_alat', 'like', "%{$search}%" );
                    } );
            } );
        }

        // Rest of the code remains the same
        $available_alat = MasterDataAlat::whereHas ( 'alatProyek', function ($query) use ($rkb)
        {
            $query->where ( 'id_proyek', $rkb->id_proyek )
                ->whereNull ( 'removed_at' );
        } )->get ();

        $stockQuantities = Saldo::where ( 'id_proyek', $rkb->id_proyek )
            ->get ()
            ->groupBy ( 'id_master_data_sparepart' )
            ->map ( function ($items)
            {
                return $items->sum ( 'quantity' );
            } );

        // Handle pagination
        if ( $perPage === -1 )
        {
            $TableData = $query->get (); // Get all records without pagination

            // Handle empty results
            if ( $TableData->isEmpty () )
            {
                $TableData = new \Illuminate\Pagination\LengthAwarePaginator(
                    collect ( [] ), // Empty collection
                    0, // Total
                    1, // Per page
                    1 // Current page
                );
            }
            else
            {
                // Convert collection to LengthAwarePaginator
                $TableData = new \Illuminate\Pagination\LengthAwarePaginator(
                    $TableData,
                    $TableData->count (),
                    max ( $TableData->count (), 1 ), // Ensure perPage is at least 1
                    1
                );
            }
        }
        else
        {
            // Regular pagination with error handling
            $total = $query->count ();
            if ( $total === 0 )
            {
                $TableData = new \Illuminate\Pagination\LengthAwarePaginator(
                    collect ( [] ), // Empty collection
                    0, // Total
                    $perPage,
                    1 // Current page
                );
            }
            else
            {
                $TableData = $query->paginate ( $perPage );
            }
        }

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        return view ( 'dashboard.evaluasi.urgent.detail.detail', [ 
            'headerPage'            => "Evaluasi Urgent",
            'page'                  => 'Detail Evaluasi Urgent [' . $rkb->proyek->nama . ' | ' . $rkb->nomor . ']',
            'menuContext'           => 'evaluasi_urgent',

            'proyeks'               => $proyeks,
            'rkb'                   => $rkb,
            'available_alat'        => $available_alat,
            'master_data_sparepart' => MasterDataSparepart::all (),
            'kategori_sparepart'    => KategoriSparepart::all (),
            'TableData'             => $TableData,
            'stockQuantities'       => $stockQuantities,
        ] );
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
            "quantity_approved"   => "required|array",
            "quantity_approved.*" => "required|integer|min:0",
        ] );

        // Ambil data dari input
        $quantityApproved = $request->input ( "quantity_approved" );

        // Loop untuk mengupdate setiap baris berdasarkan ID
        foreach ( $quantityApproved as $id => $quantity )
        {
            $updated = DetailRKBUrgent::where ( "id", $id )->update ( [ 
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
            ->route ( "evaluasi_rkb_urgent.detail.index", $id_rkb )
            ->with ( "success", "RKB Berhasil di Evaluasi!" );
    }

    public function approveVP ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // If already approved by VP, cancel approval
        if ( $rkb->is_approved_vp )
        {
            $rkb->is_approved_vp = false;
            $rkb->vp_approved_at = null;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
                ->with ( 'success', 'Approve RKB oleh VP berhasil dibatalkan!' );
        }

        // Check if can be approved by VP
        if ( ! $rkb->is_evaluated )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB harus dievaluasi terlebih dahulu!' );
        }

        $rkb->is_approved_vp = true;
        $rkb->vp_approved_at = now ();
        $rkb->save ();

        return redirect ()
            ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
            ->with ( 'success', 'RKB Berhasil di Approve oleh VP!' );
    }

    public function approveSVP ( Request $request, $id_rkb )
    {
        $rkb = RKB::find ( $id_rkb );

        // If already approved by SVP, cancel approval
        if ( $rkb->is_approved_svp )
        {
            // Reset quantity_remainder values to 0
            DetailRKBUrgent::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
            {
                $query->where ( 'id', $id_rkb );
            } )->update ( [ 'quantity_remainder' => 0 ] );

            $rkb->is_approved_svp = false;
            $rkb->svp_approved_at = null;
            $rkb->save ();

            return redirect ()
                ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
                ->with ( 'success', 'Approve RKB oleh SVP berhasil dibatalkan!' );
        }

        // Check if can be approved by SVP
        if ( ! $rkb->is_approved_vp )
        {
            return redirect ()
                ->back ()
                ->with ( 'error', 'RKB harus di-approve oleh VP terlebih dahulu!' );
        }

        // Update all DetailRKBUrgent records for this RKB
        DetailRKBUrgent::whereHas ( 'linkRkbDetails.linkAlatDetailRkb.rkb', function ($query) use ($id_rkb)
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
            ->route ( 'evaluasi_rkb_urgent.detail.index', $id_rkb )
            ->with ( 'success', 'RKB Berhasil di Approve oleh SVP!' );
    }
}
