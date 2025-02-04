<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\LinkAlatDetailRKB;
use App\Models\TimelineRKBUrgent;
use App\Http\Controllers\Controller;

class TimelineEvaluasiUrgentController extends Controller
{
    public function index ( Request $request, $id )
    {
        $proyek = Proyek::find ( $id );

        // Validate and set perPage to allowed values only
        $allowedPerPage = [ -1, 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = TimelineRKBUrgent::query ()
            ->where ( 'id_link_alat_detail_rkb', $id );

        if ( $request->has ( 'search' ) )
        {
            $search = strtolower ( $request->get ( 'search' ) );

            // Convert Indonesian month names to numbers if present
            $monthNames = [ 
                'januari'   => '01',
                'februari'  => '02',
                'maret'     => '03',
                'april'     => '04',
                'mei'       => '05',
                'juni'      => '06',
                'juli'      => '07',
                'agustus'   => '08',
                'september' => '09',
                'oktober'   => '10',
                'november'  => '11',
                'desember'  => '12'
            ];

            // Add Indonesian day names mapping to numbers (1=Monday, 7=Sunday)
            $dayNames = [ 
                'senin'  => 1,
                'selasa' => 2,
                'rabu'   => 3,
                'kamis'  => 4,
                'jumat'  => 5,
                'jumaat' => 5,
                'sabtu'  => 6,
                'minggu' => 7,
                'ahad'   => 7
            ];

            $searchMonth = array_key_exists ( $search, $monthNames ) ? $monthNames[ $search ] : $search;
            $searchDay   = array_key_exists ( $search, $dayNames ) ? $dayNames[ $search ] : null;

            $query->where ( function ($q) use ($search, $searchMonth, $searchDay)
            {
                $q->where ( 'nama_rencana', 'like', "%{$search}%" )
                    ->orWhereRaw ( "DATEDIFF(tanggal_akhir_rencana, tanggal_awal_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "DATEDIFF(tanggal_akhir_actual, tanggal_awal_actual) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "YEAR(tanggal_awal_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "YEAR(tanggal_akhir_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "YEAR(tanggal_awal_actual) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "YEAR(tanggal_akhir_actual) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "MONTH(tanggal_awal_rencana) = ?", [ (int) $searchMonth ] )
                    ->orWhereRaw ( "MONTH(tanggal_akhir_rencana) = ?", [ (int) $searchMonth ] )
                    ->orWhereRaw ( "MONTH(tanggal_awal_actual) = ?", [ (int) $searchMonth ] )
                    ->orWhereRaw ( "MONTH(tanggal_akhir_actual) = ?", [ (int) $searchMonth ] )
                    ->orWhereRaw ( "DAY(tanggal_awal_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "DAY(tanggal_akhir_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "DAY(tanggal_awal_actual) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "DAY(tanggal_akhir_actual) = ?", [ (int) $search ] )
                    ->when ( $searchDay, function ($query) use ($searchDay)
                    {
                        return $query->orWhereRaw ( "DAYOFWEEK(tanggal_awal_rencana) = ?", [ $searchDay + 1 ] )
                            ->orWhereRaw ( "DAYOFWEEK(tanggal_akhir_rencana) = ?", [ $searchDay + 1 ] )
                            ->orWhereRaw ( "DAYOFWEEK(tanggal_awal_actual) = ?", [ $searchDay + 1 ] )
                            ->orWhereRaw ( "DAYOFWEEK(tanggal_akhir_actual) = ?", [ $searchDay + 1 ] );
                    } );
            } );
        }

        $data = LinkAlatDetailRKB::with ( [ 
            'rkb',
            'masterDataAlat',
            'timelineRkbUrgents',
            'linkRkbDetails'
        ] )->find ( $id );

        // Updated TableData logic
        $TableData = $perPage === -1
            ? $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $query->count () )
            : $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $perPage );

        // Updated proyeks query with consistent sorting
        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        return view ( 'dashboard.evaluasi.urgent.detail.timeline.timeline', [ 
            'proyek'      => $proyek,
            'proyeks'     => $proyeks,
            'data'        => $data,
            'TableData'   => $TableData,
            'headerPage'  => "Evaluasi Urgent",
            'menuContext' => 'evaluasi_urgent',
            'page'        => 'Timeline Detail RKB Urgent [' . $data->rkb->proyek->nama . ' | ' . $data->rkb->nomor . ']',
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
