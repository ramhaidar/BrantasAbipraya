<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\LinkAlatDetailRKB;
use App\Models\TimelineRKBUrgent;
use App\Http\Controllers\Controller;

class TimelineEvaluasiUrgentController extends Controller
{
    public function index ( $id )
    {
        $rkb     = Proyek::find ( $id );
        $proyeks = Proyek::orderByDesc ( 'updated_at' )->get ();
        $data    = LinkAlatDetailRKB::with ( [ 
            'rkb',
            'masterDataAlat',
            'timelineRkbUrgents',
            'linkRkbDetails'
        ] )->find ( $id );

        // dd ( $data );

        // $data = TimelineRKBUrgent::find ( $id )->with ( [ 
//     'kategoriSparepart',
//     'masterDataSparepart',
//     'linkAlatDetailRkb',
// ] )->get ();

        // Filter $data that have 

        // sort $data by timeline_rkb_urgent id
// $data = $data->sortBy ( 'id' );

        // dd ( $data );

        return view ( 'dashboard.evaluasi.urgent.detail.timeline.timeline', [ 
            'rkb'        => $rkb,
            'proyeks'    => $proyeks,
            'data'       => $data,

            'headerPage' => "RKB Urgent",
            'page'       => 'Timeline Detail RKB Urgent',
        ] );
    }

    public function show ( $id )
    {
        $data = TimelineRKBUrgent::find ( $id );

        if ( ! $data )
        {
            return response ()->json ( [ 
                'error' => 'Detail RKB Urgent not found!',
            ], 404 );
        }

        // Format respons
        return response ()->json ( [ 
            'data' => $data,
        ] );
    }

    public function store ( Request $request )
    {
        // Validasi input
        $request->validate ( [ 
            'id_link_alat_detail_rkb' => 'required|integer|exists:link_alat_detail_rkb,id',
            'uraian_pekerjaan'        => 'required|string|max:255',
            'tanggal_awal_rencana'    => 'required|date',
            'tanggal_akhir_rencana'   => 'required|date|after_or_equal:tanggal_awal_rencana',
        ] );

        try
        {
            // Simpan data ke database
            TimelineRKBUrgent::create ( [ 
                'nama_rencana'            => $request->uraian_pekerjaan,
                'tanggal_awal_rencana'    => $request->tanggal_awal_rencana,
                'tanggal_akhir_rencana'   => $request->tanggal_akhir_rencana,
                'id_link_alat_detail_rkb' => $request->id_link_alat_detail_rkb,
            ] );

            // Redirect dengan pesan sukses
            return redirect ()->back ()->with ( 'success', 'Data pekerjaan berhasil ditambahkan.' );
        }
        catch ( \Exception $e )
        {
            // Redirect dengan pesan error jika terjadi masalah
            return redirect ()->back ()->with ( 'error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage () );
        }
    }

    public function update ( Request $request, $id )
    {
        // Validasi input
        $request->validate ( [ 
            'uraian_pekerjaan'      => 'required|string|max:255',
            'tanggal_awal_rencana'  => 'required|date',
            'tanggal_akhir_rencana' => 'required|date|after_or_equal:tanggal_awal_rencana',
            'tanggal_awal_actual'   => 'nullable|date',
            'tanggal_akhir_actual'  => 'nullable|date|after_or_equal:tanggal_awal_actual',
        ] );

        try
        {
            // Temukan data berdasarkan ID
            $timeline = TimelineRKBUrgent::findOrFail ( $id );

            // Perbarui data di database
            $timeline->update ( [ 
                'nama_rencana'          => $request->uraian_pekerjaan,
                'tanggal_awal_rencana'  => $request->tanggal_awal_rencana,
                'tanggal_akhir_rencana' => $request->tanggal_akhir_rencana,
                'tanggal_awal_actual'   => $request->tanggal_awal_actual,
                'tanggal_akhir_actual'  => $request->tanggal_akhir_actual,
                'is_done'               => $request->tanggal_awal_actual && $request->tanggal_akhir_actual ? true : false,
            ] );

            // Redirect dengan pesan sukses
            return redirect ()->back ()->with ( 'success', 'Data pekerjaan berhasil diperbarui.' );
        }
        catch ( \Exception $e )
        {
            // Redirect dengan pesan error jika terjadi masalah
            return redirect ()->back ()->with ( 'error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage () );
        }
    }

    public function destroy ( $id )
    {
        try
        {
            // Temukan data berdasarkan ID
            $timeline = TimelineRKBUrgent::findOrFail ( $id );

            // Hapus data dari database
            $timeline->delete ();

            // Redirect dengan pesan sukses
            return redirect ()->back ()->with ( 'success', 'Data pekerjaan berhasil dihapus.' );
        }
        catch ( \Exception $e )
        {
            // Redirect dengan pesan error jika terjadi masalah
            return redirect ()->back ()->with ( 'error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage () );
        }
    }
}
