<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\LinkAlatDetailRKB;
use App\Models\TimelineRKBUrgent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TimelineEvaluasiUrgentController extends Controller
{
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];

        try
        {
            // Decode base64 and split by custom separator
            $decodedValue = base64_decode ( $paramValue );

            // Split by || for regular values and preserve special numeric filters
            $values = [];
            $parts  = explode ( '||', $decodedValue );

            foreach ( $parts as $part )
            {
                if (
                    strpos ( $part, 'exact:' ) === 0 ||
                    strpos ( $part, 'gt:' ) === 0 ||
                    strpos ( $part, 'lt:' ) === 0 ||
                    $part === 'null'
                )
                {
                    $values[] = $part;
                }
                else
                {
                    $values[] = trim ( $part );
                }
            }

            return $values;
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
                if ( in_array ( 'Empty/Null', $uraian ) )
                {
                    $nonNullValues = array_filter ( $uraian, fn ( $value ) => $value !== 'Empty/Null' );
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
                $status        = $this->getSelectedValues ( $request->selected_status );
                $statusMap     = [ 
                    'Sudah Selesai' => true,
                    'Belum Selesai' => false
                ];
                $booleanValues = array_map ( function ($s) use ($statusMap)
                {
                    return $statusMap[ $s ] ?? null;
                }, $status );

                $query->whereIn ( 'is_done', array_filter ( $booleanValues, function ($value)
                {
                    return $value !== null;
                } ) );
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
                    $dateValues = $this->getSelectedValues ( $request->get ( "selected_{$field}" ) );

                    $query->where ( function ($q) use ($dateValues, $field)
                    {
                        $hasRange = false;
                        $gtDate   = null;
                        $ltDate   = null;

                        foreach ( $dateValues as $value )
                        {
                            if ( $value === 'Empty/Null' )
                            {
                                $q->orWhereNull ( $field );
                            }
                            elseif ( strpos ( $value, 'exact:' ) === 0 )
                            {
                                $date = substr ( $value, 6 );
                                $q->orWhereDate ( $field, '=', $date );
                            }
                            elseif ( strpos ( $value, 'gt:' ) === 0 )
                            {
                                $gtDate   = substr ( $value, 3 );
                                $hasRange = true;
                            }
                            elseif ( strpos ( $value, 'lt:' ) === 0 )
                            {
                                $ltDate   = substr ( $value, 3 );
                                $hasRange = true;
                            }
                        }

                        // Handle date range if present
                        if ( $hasRange )
                        {
                            if ( $gtDate && $ltDate )
                            {
                                $q->orWhereBetween ( $field, [ $gtDate, $ltDate ] );
                            }
                            elseif ( $gtDate )
                            {
                                $q->orWhereDate ( $field, '>=', $gtDate );
                            }
                            elseif ( $ltDate )
                            {
                                $q->orWhereDate ( $field, '<=', $ltDate );
                            }
                        }
                    } );
                }
                catch ( \Exception $e )
                {
                    \Log::error ( "Error in {$field} filter: " . $e->getMessage () );
                }
            }
        }

        // Handle durasi fields with numeric filtering
        $durasiFields = [ 'durasi_rencana', 'durasi_actual' ];
        foreach ( $durasiFields as $field )
        {
            if ( $request->filled ( "selected_{$field}" ) )
            {
                try
                {
                    $values = $this->getSelectedValues ( $request->get ( "selected_{$field}" ) );

                    $query->where ( function ($q) use ($values, $field)
                    {
                        $exactValues = [];
                        $gtValue     = null;
                        $ltValue     = null;
                        $hasNull     = false;

                        foreach ( $values as $value )
                        {
                            if ( $value === 'Empty/Null' || $value === 'null' )
                            {
                                $hasNull = true;
                            }
                            elseif ( strpos ( $value, 'exact:' ) === 0 )
                            {
                                $exactValues[] = (int) substr ( $value, 6 );
                            }
                            elseif ( strpos ( $value, 'gt:' ) === 0 )
                            {
                                $gtValue = (int) substr ( $value, 3 );
                            }
                            elseif ( strpos ( $value, 'lt:' ) === 0 )
                            {
                                $ltValue = (int) substr ( $value, 3 );
                            }
                        }

                        if ( $field === 'durasi_actual' )
                        {
                            if ( $hasNull )
                            {
                                $q->orWhereNull ( 'tanggal_awal_actual' )
                                    ->orWhereNull ( 'tanggal_akhir_actual' )
                                    ->orWhereRaw ( 'tanggal_awal_actual = tanggal_akhir_actual' );
                            }

                            if ( ! empty ( $exactValues ) )
                            {
                                foreach ( $exactValues as $value )
                                {
                                    $q->orWhereRaw (
                                        'EXTRACT(DAY FROM (tanggal_akhir_actual::timestamp - tanggal_awal_actual::timestamp))::integer = ?',
                                        [ $value ]
                                    );
                                }
                            }
                            if ( $gtValue !== null && $ltValue !== null )
                            {
                                $q->orWhereRaw (
                                    'EXTRACT(DAY FROM (tanggal_akhir_actual::timestamp - tanggal_awal_actual::timestamp))::integer BETWEEN ? AND ?',
                                    [ $gtValue, $ltValue ]
                                );
                            }
                            elseif ( $gtValue !== null )
                            {
                                $q->orWhereRaw (
                                    'EXTRACT(DAY FROM (tanggal_akhir_actual::timestamp - tanggal_awal_actual::timestamp))::integer >= ?',
                                    [ $gtValue ]
                                );
                            }
                            elseif ( $ltValue !== null )
                            {
                                $q->orWhereRaw (
                                    'EXTRACT(DAY FROM (tanggal_akhir_actual::timestamp - tanggal_awal_actual::timestamp))::integer <= ?',
                                    [ $ltValue ]
                                );
                            }
                        }
                        else
                        {
                            if ( $hasNull )
                            {
                                $q->orWhereNull ( 'tanggal_awal_rencana' )
                                    ->orWhereNull ( 'tanggal_akhir_rencana' )
                                    ->orWhereRaw ( 'tanggal_awal_rencana = tanggal_akhir_rencana' );
                            }

                            if ( ! empty ( $exactValues ) )
                            {
                                foreach ( $exactValues as $value )
                                {
                                    $q->orWhereRaw (
                                        'EXTRACT(DAY FROM (tanggal_akhir_rencana::timestamp - tanggal_awal_rencana::timestamp))::integer = ?',
                                        [ $value ]
                                    );
                                }
                            }
                            if ( $gtValue !== null && $ltValue !== null )
                            {
                                $q->orWhereRaw (
                                    'EXTRACT(DAY FROM (tanggal_akhir_rencana::timestamp - tanggal_awal_rencana::timestamp))::integer BETWEEN ? AND ?',
                                    [ $gtValue, $ltValue ]
                                );
                            }
                            elseif ( $gtValue !== null )
                            {
                                $q->orWhereRaw (
                                    'EXTRACT(DAY FROM (tanggal_akhir_rencana::timestamp - tanggal_awal_rencana::timestamp))::integer >= ?',
                                    [ $gtValue ]
                                );
                            }
                            elseif ( $ltValue !== null )
                            {
                                $q->orWhereRaw (
                                    'EXTRACT(DAY FROM (tanggal_akhir_rencana::timestamp - tanggal_akhir_rencana::timestamp))::integer <= ?',
                                    [ $ltValue ]
                                );
                            }
                        }
                    } );
                }
                catch ( \Exception $e )
                {
                    \Log::error ( "Error in {$field} filter: " . $e->getMessage () );
                }
            }
        }

        return $query;
    }

    private function getUniqueValues ( $id, $filteredQuery = null )
    {
        // If no filtered query is provided, create a base query
        if ( ! $filteredQuery )
        {
            $filteredQuery = TimelineRKBUrgent::where ( 'id_link_alat_detail_rkb', $id );
        }

        // Get filtered results
        $filteredResults = $filteredQuery->get ();

        return [ 
            'uraian'                => $filteredResults->pluck ( 'nama_rencana' )->unique ()->values (),
            'durasi_rencana'        => $filteredResults
                ->whereNotNull ( 'tanggal_awal_rencana' )
                ->whereNotNull ( 'tanggal_akhir_rencana' )
                ->map ( function ($item)
                {
                    // Take absolute value of the difference for positive duration
                    return abs ( $item->tanggal_akhir_rencana->diffInDays ( $item->tanggal_awal_rencana ) );
                } )
                ->unique ()
                ->sort () // Sort the durations numerically
                ->values (),
            'tanggal_awal_rencana'  => $filteredResults
                ->whereNotNull ( 'tanggal_awal_rencana' )
                ->pluck ( 'tanggal_awal_rencana' )
                ->map ( fn ( $date ) => $date->format ( 'Y-m-d' ) )
                ->unique ()
                ->values (),
            'tanggal_akhir_rencana' => $filteredResults
                ->whereNotNull ( 'tanggal_akhir_rencana' )
                ->pluck ( 'tanggal_akhir_rencana' )
                ->map ( fn ( $date ) => $date->format ( 'Y-m-d' ) )
                ->unique ()
                ->values (),
            'durasi_actual'         => $filteredResults
                ->whereNotNull ( 'tanggal_awal_actual' )
                ->whereNotNull ( 'tanggal_akhir_actual' )
                ->map ( function ($item)
                {
                    // Take absolute value of the difference for positive duration
                    return abs ( $item->tanggal_akhir_actual->diffInDays ( $item->tanggal_awal_actual ) );
                } )
                ->unique ()
                ->sort () // Sort the durations numerically
                ->values (),
            'tanggal_awal_actual'   => $filteredResults
                ->whereNotNull ( 'tanggal_awal_actual' )
                ->pluck ( 'tanggal_awal_actual' )
                ->map ( fn ( $date ) => $date->format ( 'Y-m-d' ) )
                ->unique ()
                ->values (),
            'tanggal_akhir_actual'  => $filteredResults
                ->whereNotNull ( 'tanggal_akhir_actual' )
                ->pluck ( 'tanggal_akhir_actual' )
                ->map ( fn ( $date ) => $date->format ( 'Y-m-d' ) )
                ->unique ()
                ->values (),
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

        $proyek = Proyek::find ( $id );

        $query = $this->buildQuery ( $request, $id );

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
                $q->where ( 'nama_rencana', 'ilike', "%{$search}%" )
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

        $uniqueValues = $this->getUniqueValues ( $id, clone $query );

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

        return view ( 'dashboard.evaluasi.urgent.detail.timeline.timeline', [ 
            'proyek'       => $proyek,
            'proyeks'      => $proyeks,
            'data'         => $data,
            'TableData'    => $TableData,
            'headerPage'   => "Evaluasi Urgent",
            'menuContext'  => 'evaluasi_urgent',
            'page'         => 'Evaluasi Timeline Detail RKB Urgent [' . $data->rkb->proyek->nama . ' | ' . $data->rkb->nomor . ']',
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
