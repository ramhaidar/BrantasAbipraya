<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\DetailRKBUrgent;
use App\Models\LinkAlatDetailRKB;
use App\Models\TimelineRKBUrgent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TimelineRKBUrgentController extends Controller
{
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];

        try
        {
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            \Log::error ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
    }

    private function buildQuery ( $request, $id )
    {
        $query = TimelineRKBUrgent::query ()
            ->where ( 'id_link_alat_detail_rkb', $id );

        // Handle uraian filter
        if ( $request->filled ( 'selected_uraian' ) )
        {
            try
            {
                $uraian = $this->getSelectedValues ( $request->selected_uraian );
                if ( in_array ( 'null', $uraian ) )
                {
                    $nonNullValues = array_filter ( $uraian, fn ( $value ) => $value !== 'null' );
                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        $q->whereNull ( 'nama_rencana' )
                            ->orWhere ( 'nama_rencana', '' )
                            ->when ( count ( $nonNullValues ) > 0, function ($q) use ($nonNullValues)
                            {
                                $q->orWhereIn ( 'nama_rencana', $nonNullValues );
                            } );
                    } );
                }
                else
                {
                    $query->whereIn ( 'nama_rencana', $uraian );
                }
            }
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in uraian filter: ' . $e->getMessage () );
            }
        }

        // Handle status filter
        if ( $request->filled ( 'selected_status' ) )
        {
            try
            {
                $status = $this->getSelectedValues ( $request->selected_status );
                $query->whereIn ( 'is_done', $status );
            }
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in status filter: ' . $e->getMessage () );
            }
        }

        // Handle date fields
        $dateFields = [ 
            'tanggal_awal_rencana',
            'tanggal_akhir_rencana',
            'tanggal_awal_actual',
            'tanggal_akhir_actual'
        ];

        foreach ( $dateFields as $field )
        {
            if ( $request->filled ( "selected_{$field}" ) )
            {
                try
                {
                    $dates = $this->getSelectedValues ( $request->get ( "selected_{$field}" ) );
                    if ( in_array ( 'null', $dates ) )
                    {
                        $nonNullDates = array_filter ( $dates, fn ( $date ) => $date !== 'null' );
                        $query->where ( function ($q) use ($nonNullDates, $field)
                        {
                            $q->whereNull ( $field )
                                ->when ( count ( $nonNullDates ) > 0, function ($q) use ($nonNullDates, $field)
                                {
                                    $q->orWhereIn ( \DB::raw ( "DATE({$field})" ), $nonNullDates );
                                } );
                        } );
                    }
                    else
                    {
                        $query->whereIn ( \DB::raw ( "DATE({$field})" ), $dates );
                    }
                }
                catch ( \Exception $e )
                {
                    \Log::error ( "Error in {$field} filter: " . $e->getMessage () );
                }
            }
        }

        // Handle durasi fields
        $durasiFields = [ 'durasi_rencana', 'durasi_actual' ];
        foreach ( $durasiFields as $field )
        {
            if ( $request->filled ( "selected_{$field}" ) )
            {
                try
                {
                    $durasi = $this->getSelectedValues ( $request->get ( "selected_{$field}" ) );
                    if ( in_array ( 'null', $durasi ) )
                    {
                        $nonNullValues = array_filter ( $durasi, fn ( $value ) => $value !== 'null' );
                        if ( $field === 'durasi_actual' )
                        {
                            $query->where ( function ($q) use ($nonNullValues)
                            {
                                $q->whereNull ( 'tanggal_awal_actual' )
                                    ->orWhereNull ( 'tanggal_akhir_actual' )
                                    ->when ( count ( $nonNullValues ) > 0, function ($q) use ($nonNullValues)
                                    {
                                        $q->orWhereRaw (
                                            'EXTRACT(DAY FROM (tanggal_akhir_actual::timestamp - tanggal_awal_actual::timestamp))::integer = ANY(?)',
                                            [ "{" . implode ( ',', $nonNullValues ) . "}" ]
                                        );
                                    } );
                            } );
                        }
                        else
                        {
                            $query->where ( function ($q) use ($nonNullValues)
                            {
                                $q->whereNull ( 'tanggal_awal_rencana' )
                                    ->orWhereNull ( 'tanggal_akhir_rencana' )
                                    ->when ( count ( $nonNullValues ) > 0, function ($q) use ($nonNullValues)
                                    {
                                        $q->orWhereRaw (
                                            'EXTRACT(DAY FROM (tanggal_akhir_rencana::timestamp - tanggal_awal_rencana::timestamp))::integer = ANY(?)',
                                            [ "{" . implode ( ',', $nonNullValues ) . "}" ]
                                        );
                                    } );
                            } );
                        }
                    }
                    else
                    {
                        if ( $field === 'durasi_actual' )
                        {
                            $query->whereRaw (
                                'EXTRACT(DAY FROM (tanggal_akhir_actual::timestamp - tanggal_awal_actual::timestamp))::integer = ANY(?)',
                                [ "{" . implode ( ',', $durasi ) . "}" ]
                            );
                        }
                        else
                        {
                            $query->whereRaw (
                                'EXTRACT(DAY FROM (tanggal_akhir_rencana::timestamp - tanggal_awal_rencana::timestamp))::integer = ANY(?)',
                                [ "{" . implode ( ',', $durasi ) . "}" ]
                            );
                        }
                    }
                }
                catch ( \Exception $e )
                {
                    \Log::error ( "Error in {$field} filter: " . $e->getMessage () );
                }
            }
        }

        return $query;
    }

    private function getUniqueValues ( $id )
    {
        $timelines = TimelineRKBUrgent::where ( 'id_link_alat_detail_rkb', $id );

        return [ 
            'uraian'                => $timelines->clone ()->distinct ()->pluck ( 'nama_rencana' ),
            'durasi_rencana'        => $timelines->clone ()
                ->whereNotNull ( 'tanggal_awal_rencana' )
                ->whereNotNull ( 'tanggal_akhir_rencana' )
                ->selectRaw ( 'DISTINCT EXTRACT(DAY FROM (tanggal_akhir_rencana::timestamp - tanggal_awal_rencana::timestamp))::integer as days' )
                ->pluck ( 'days' ),
            'tanggal_awal_rencana'  => $timelines->clone ()
                ->whereNotNull ( 'tanggal_awal_rencana' )
                ->distinct ()
                ->pluck ( 'tanggal_awal_rencana' )
                ->map ( fn ( $date ) => $date->format ( 'Y-m-d' ) ),
            'tanggal_akhir_rencana' => $timelines->clone ()
                ->whereNotNull ( 'tanggal_akhir_rencana' )
                ->distinct ()
                ->pluck ( 'tanggal_akhir_rencana' )
                ->map ( fn ( $date ) => $date->format ( 'Y-m-d' ) ),
            'durasi_actual'         => $timelines->clone ()
                ->whereNotNull ( 'tanggal_awal_actual' )
                ->whereNotNull ( 'tanggal_akhir_actual' )
                ->selectRaw ( 'DISTINCT EXTRACT(DAY FROM (tanggal_akhir_actual::timestamp - tanggal_awal_actual::timestamp))::integer as days' )
                ->pluck ( 'days' ),
            'tanggal_awal_actual'   => $timelines->clone ()
                ->whereNotNull ( 'tanggal_awal_actual' )
                ->distinct ()
                ->pluck ( 'tanggal_awal_actual' )
                ->map ( fn ( $date ) => $date->format ( 'Y-m-d' ) ),
            'tanggal_akhir_actual'  => $timelines->clone ()
                ->whereNotNull ( 'tanggal_akhir_actual' )
                ->distinct ()
                ->pluck ( 'tanggal_akhir_actual' )
                ->map ( fn ( $date ) => $date->format ( 'Y-m-d' ) ),
        ];
    }

    public function index ( Request $request, $id )
    {
        if ( $request->get ( 'per_page' ) != -1 )
        {
            $parameters               = $request->except ( 'per_page' );
            $parameters[ 'per_page' ] = -1;

            return redirect ()->to ( $request->url () . '?' . http_build_query ( $parameters ) );
        }

        $perPage = (int) $request->per_page;

        // Get RKB data
        $rkb = Proyek::find ( $id );

        $query        = $this->buildQuery ( $request, $id );
        $uniqueValues = $this->getUniqueValues ( $id );

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
                'jumaat' => 5, // alternative spelling
                'sabtu'  => 6,
                'minggu' => 7,
                'ahad'   => 7 // alternative name
            ];

            $searchMonth = array_key_exists ( $search, $monthNames ) ? $monthNames[ $search ] : $search;
            $searchDay   = array_key_exists ( $search, $dayNames ) ? $dayNames[ $search ] : null;

            $query->where ( function ($q) use ($search, $searchMonth, $searchDay)
            {
                $q->where ( 'nama_rencana', 'ilike', "%{$search}%" )
                    // Search by day difference
                    ->orWhereRaw ( "DATEDIFF(tanggal_akhir_rencana, tanggal_awal_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "DATEDIFF(tanggal_akhir_actual, tanggal_awal_actual) = ?", [ (int) $search ] )
                    // Search by year in any date field
                    ->orWhereRaw ( "YEAR(tanggal_awal_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "YEAR(tanggal_akhir_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "YEAR(tanggal_awal_actual) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "YEAR(tanggal_akhir_actual) = ?", [ (int) $search ] )
                    // Search by month in any date field
                    ->orWhereRaw ( "MONTH(tanggal_awal_rencana) = ?", [ (int) $searchMonth ] )
                    ->orWhereRaw ( "MONTH(tanggal_akhir_rencana) = ?", [ (int) $searchMonth ] )
                    ->orWhereRaw ( "MONTH(tanggal_awal_actual) = ?", [ (int) $searchMonth ] )
                    ->orWhereRaw ( "MONTH(tanggal_akhir_actual) = ?", [ (int) $searchMonth ] )
                    // Search by day in any date field
                    ->orWhereRaw ( "DAY(tanggal_awal_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "DAY(tanggal_akhir_rencana) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "DAY(tanggal_awal_actual) = ?", [ (int) $search ] )
                    ->orWhereRaw ( "DAY(tanggal_akhir_actual) = ?", [ (int) $search ] )
                    // Add weekday searches if a day name was provided
                    ->when ( $searchDay, function ($query) use ($searchDay)
                    {
                        return $query->orWhereRaw ( "DAYOFWEEK(tanggal_awal_rencana) = ?", [ $searchDay + 1 ] )
                            ->orWhereRaw ( "DAYOFWEEK(tanggal_akhir_rencana) = ?", [ $searchDay + 1 ] )
                            ->orWhereRaw ( "DAYOFWEEK(tanggal_awal_actual) = ?", [ $searchDay + 1 ] )
                            ->orWhereRaw ( "DAYOFWEEK(tanggal_akhir_actual) = ?", [ $searchDay + 1 ] );
                    } );
            } );
        }

        // Filter projects based on user role
        $user         = Auth::user ();
        $proyeksQuery = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        $proyeks = $proyeksQuery
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $data = LinkAlatDetailRKB::with ( [ 
            'rkb',
            'masterDataAlat',
            'timelineRkbUrgents',
            'linkRkbDetails'
        ] )->find ( $id );

        // Handle pagination with consistent sorting
        if ( $perPage === -1 )
        {
            $queryData = $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->get ();

            $TableData = new \Illuminate\Pagination\LengthAwarePaginator(
                $queryData,
                $queryData->count (),
                -1,
                1,
                [ 
                    'path'  => $request->url (),
                    'query' => $request->query (),
                ]
            );
        }
        else
        {
            // Check if any results exist
            if ( $query->count () > 0 )
            {
                $TableData = $query->orderBy ( 'updated_at', 'desc' )
                    ->orderBy ( 'id', 'desc' )
                    ->paginate ( $perPage )
                    ->withQueryString ();
            }
            else
            {
                // Return empty paginator if no results
                $TableData = new \Illuminate\Pagination\LengthAwarePaginator(
                    [],
                    0,
                    $perPage,
                    1,
                    [ 
                        'path'  => $request->url (),
                        'query' => $request->query (),
                    ]
                );
            }
        }

        return view ( 'dashboard.rkb.urgent.detail.timeline.timeline', [ 
            'rkb'          => $rkb,
            'data'         => $data,
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'headerPage'   => 'RKB Urgent',
            'menuContext'  => 'rkb_urgent', // Specific to Urgent
            'page'         => 'Timeline Detail RKB Urgent [' . $data->rkb->nomor . ' | ' . $data->masterDataAlat->jenis_alat . ' : ' . $data->masterDataAlat->kode_alat . ']',
            'uniqueValues' => $uniqueValues,
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
