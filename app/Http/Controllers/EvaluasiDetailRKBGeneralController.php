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

        return view ( "dashboard.evaluasi.general.detail.detail", [ 
            "rkb"                   => $rkb,
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
        // Validasi input untuk memastikan semua data sesuai
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

    public function getData ( Request $request, $id_rkb )
    {
        // Base query dengan relasi dan alias untuk kolom relasi
        $query = DetailRKBGeneral::query ()
            ->select ( [ 
                "detail_rkb_general.*", // Kolom utama
                "kategori_sparepart.kode as kategoriSparepartCode", // Alias untuk kode kategori sparepart
                "kategori_sparepart.nama as kategoriSparepartName", // Alias untuk nama kategori sparepart
                "master_data_sparepart.nama as sparepartName", // Alias untuk nama sparepart
                "master_data_sparepart.part_number as partNumber", // Alias untuk part number
                "master_data_sparepart.merk as merk", // Alias untuk merk
            ] )
            ->leftJoin (
                "kategori_sparepart",
                "detail_rkb_general.id_kategori_sparepart_sparepart",
                "=",
                "kategori_sparepart.id"
            )
            ->leftJoin (
                "master_data_sparepart",
                "detail_rkb_general.id_master_data_sparepart",
                "=",
                "master_data_sparepart.id"
            )
            ->whereHas (
                "linkRkbDetails.linkAlatDetailRkb.rkb",

                function ($q) use ($id_rkb)
                {
                    $q->where ( "id", $id_rkb );
                }
            );

        // Pagination parameters
        $draw   = $request->input ( "draw" );
        $start  = $request->input ( "start", 0 );
        $length = $request->input ( "length", 10 );

        // Sorting berdasarkan index kolom
        if ( $order = $request->input ( "order" ) )
        {
            $columnIndex   = $order[ 0 ][ "column" ];
            $sortDirection = $order[ 0 ][ "dir" ];

            switch ($columnIndex)
            {
                case 4: // Part Number
                    $query->orderBy ( "partNumber", $sortDirection );
                    break;

                default:
                    $query->orderBy ( "detail_rkb_general.created_at", "desc" ); // Default sorting
                    break;
            }
        }

        // Filter pencarian
        if ( $search = $request->input ( "search.value" ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where (
                    "detail_rkb_general.quantity_requested",
                    "LIKE",
                    "%$search%"
                )
                    ->orWhere (
                        "detail_rkb_general.quantity_approved",
                        "LIKE",
                        "%$search%"
                    )
                    ->orWhere ( "detail_rkb_general.satuan", "LIKE", "%$search%" )
                    ->orWhere ( "kategori_sparepart.kode", "LIKE", "%$search%" )
                    ->orWhere ( "kategori_sparepart.nama", "LIKE", "%$search%" )
                    ->orWhere ( "master_data_sparepart.nama", "LIKE", "%$search%" )
                    ->orWhere (
                        "master_data_sparepart.part_number",
                        "LIKE",
                        "%$search%"
                    )
                    ->orWhere (
                        "master_data_sparepart.merk",
                        "LIKE",
                        "%$search%"
                    );
            } );
        }

        // Total records
        $totalRecords = $query->count ();

        // Ambil data dengan paginasi
        $data = $query
            ->skip ( $start )
            ->take ( $length )
            ->get ();

        // Get the RKB to access its status flags
        $rkb = RKB::findOrFail ( $id_rkb );

        // Format data untuk DataTable
        $formattedData = $data->map ( function ($item) use ($rkb)
        {
            return [ 
                "id"                  => $item->id,
                "masterDataAlat"      =>
                    optional (
                        optional ( $item->linkRkbDetails->first () )
                            ->linkAlatDetailRkb->masterDataAlat
                    )->jenis_alat ?? "-",
                "kodeAlat"            =>
                    optional (
                        optional ( $item->linkRkbDetails->first () )
                            ->linkAlatDetailRkb->masterDataAlat
                    )->kode_alat ?? "-",
                "kategoriSparepart"   =>
                    ( $item->kategoriSparepartCode
                        ? "{$item->kategoriSparepartCode}: "
                        : "" ) .
                    ( $item->kategoriSparepartName ?? "-" ), // Format Kode: Kategori
                "masterDataSparepart" => $item->sparepartName ?? "-",
                "partNumber"          => $item->partNumber ?? "-", // Menggunakan alias partNumber
                "merk"                => $item->merk ?? "-",
                "quantity_requested"  => $item->quantity_requested,
                "quantity_approved"   => $item->quantity_approved,
                "quantity_in_stock"   => $item->quantity_in_stock ?? 0,
                "satuan"              => $item->satuan,
                // Add the status flags from RKB
                'is_approved'         => $rkb->is_approved,
                'is_finalized'        => $rkb->is_finalized,
                'is_evaluated'        => $rkb->is_evaluated
            ];
        } );

        return response ()->json ( [ 
            "draw"            => $draw,
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data"            => $formattedData->values (),
        ] );
    }
}
