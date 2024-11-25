<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Models\DetailRKBGeneral;
use App\Models\KategoriSparepart;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;

class EvaluasiDetailRKBGeneralController extends Controller
{
    public function index ( $id )
    {
        $rkb                   = RKB::with ( [ 'proyek', 'LinkRKBDetails' ] )->find ( $id );
        $proyeks               = Proyek::orderByDesc ( 'updated_at' )->get ();
        $master_data_alat      = MasterDataAlat::all ();
        $master_data_sparepart = MasterDataSparepart::all ();
        $kategori_sparepart    = KategoriSparepart::all ();

        return view ( 'dashboard.evaluasi.general.detail.detail', [ 
            'rkb'                   => $rkb,
            'proyeks'               => $proyeks,
            'master_data_alat'      => $master_data_alat,
            'master_data_sparepart' => $master_data_sparepart,
            'kategori_sparepart'    => $kategori_sparepart,

            'headerPage'            => "Evaluasi General",
            'page'                  => 'Detail Evaluasi General',
        ] );
    }

    public function approve ( Request $request, $id_rkb )
    {
        // Validasi input untuk memastikan semua data sesuai
        $request->validate ( [ 
            'quantity_approved'   => 'required|array',
            'quantity_approved.*' => 'required|integer|min:0',
        ] );

        // Ambil data dari input
        $quantityApproved = $request->input ( 'quantity_approved' );

        // Loop untuk mengupdate setiap baris berdasarkan ID
        foreach ( $quantityApproved as $id => $quantity )
        {
            $updated = DetailRKBGeneral::where ( 'id', $id )->update ( [ 'quantity_approved' => $quantity ] );

            // Debug jika update gagal
            if ( ! $updated )
            {
                return redirect ()
                    ->back ()
                    ->with ( 'error', "Gagal mengupdate data untuk ID {$id}" );
            }
        }

        $rkb              = RKB::find ( $id_rkb );
        $rkb->is_approved = true;
        $rkb->save ();

        // Redirect dengan pesan sukses
        return redirect ()
            ->route ( 'evaluasi_rkb_general.detail.index', $id_rkb )
            ->with ( 'success', 'RKB Approved successfully!' );
    }

    public function getData ( Request $request, $id_rkb )
    {
        // Base query with joins for related tables
        $query = DetailRKBGeneral::query ()
            ->join ( 'link_rkb_detail', 'link_rkb_detail.id_detail_rkb_general', '=', 'detail_rkb_general.id' )
            ->join ( 'rkb', 'link_rkb_detail.id_rkb', '=', 'rkb.id' ) // Join with RKB table
            ->leftJoin ( 'master_data_alat', 'master_data_alat.id', '=', 'detail_rkb_general.id_master_data_alat' )
            ->leftJoin ( 'kategori_sparepart', 'kategori_sparepart.id', '=', 'detail_rkb_general.id_kategori_sparepart_sparepart' )
            ->leftJoin ( 'master_data_sparepart', 'master_data_sparepart.id', '=', 'detail_rkb_general.id_master_data_sparepart' )
            ->select (
                'detail_rkb_general.*',
                'master_data_alat.jenis_alat as masterDataAlat',
                \DB::raw ( "CONCAT(kategori_sparepart.kode, ': ', kategori_sparepart.nama) as kategoriSparepart" ),
                'master_data_sparepart.nama as masterDataSparepart',
                'rkb.is_finalized' // Include is_finalized
            )
            ->where ( 'link_rkb_detail.id_rkb', $id_rkb ); // Filter berdasarkan id_rkb

        // Handle search input
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'quantity_requested', 'like', "%{$search}%" )
                    ->orWhere ( 'quantity_approved', 'like', "%{$search}%" )
                    ->orWhere ( 'satuan', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_alat.jenis_alat', 'like', "%{$search}%" )
                    ->orWhere ( \DB::raw ( "CONCAT(kategori_sparepart.kode, ': ', kategori_sparepart.nama)" ), 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'rkb.is_finalized', 'like', "%{$search}%" ); // Allow search on is_finalized
            } );
        }

        // Handle ordering
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];

            if ( $columnName === 'masterDataAlat' )
            {
                $query->orderBy ( 'master_data_alat.jenis_alat', $sortDirection );
            }
            elseif ( $columnName === 'kategoriSparepart' )
            {
                $query->orderBy ( 'kategori_sparepart.kode', $sortDirection )
                    ->orderBy ( 'kategori_sparepart.nama', $sortDirection );
            }
            elseif ( $columnName === 'masterDataSparepart' )
            {
                $query->orderBy ( 'master_data_sparepart.nama', $sortDirection );
            }
            elseif ( $columnName === 'is_finalized' )
            {
                $query->orderBy ( 'rkb.is_finalized', $sortDirection );
            }
            elseif ( in_array ( $columnName, [ 'quantity_requested', 'quantity_approved', 'satuan' ] ) )
            {
                $query->orderBy ( $columnName, $sortDirection );
            }
        }
        else
        {
            $query->orderBy ( 'detail_rkb_general.updated_at', 'desc' );
        }

        // Calculate pagination parameters
        $draw   = $request->input ( 'draw' );
        $start  = $request->input ( 'start', 0 );
        $length = $request->input ( 'length', 10 );

        $totalRecords = DetailRKBGeneral::join ( 'link_rkb_detail', 'link_rkb_detail.id_detail_rkb_general', '=', 'detail_rkb_general.id' )
            ->where ( 'link_rkb_detail.id_rkb', $id_rkb )
            ->count ();

        $filteredRecords = $query->count ();

        // Fetch the data with pagination
        $data = $query->skip ( $start )->take ( $length )->get ()->map ( function ($item)
        {
            return [ 
                'id'                  => $item->id,
                'masterDataAlat'      => $item->masterDataAlat ?? '-',
                'kategoriSparepart'   => $item->kategoriSparepart ?? '-',
                'masterDataSparepart' => $item->masterDataSparepart ?? '-',
                'quantity_requested'  => $item->quantity_requested,
                'quantity_approved'   => $item->quantity_approved,
                'satuan'              => $item->satuan,
                'is_finalized'        => $item->is_finalized, // Convert to human-readable format
            ];
        } );

        return response ()->json ( [ 
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ] );
    }
}
