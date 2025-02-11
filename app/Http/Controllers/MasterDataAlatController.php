<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use Illuminate\Support\Facades\Auth;

class MasterDataAlatController extends Controller
{
    public function index ( Request $request )
    {
        $perPage = $this->getPerPage ( $request );
        $query   = $this->buildBaseQuery ( $request );

        $this->applySearchFilter ( $request, $query );
        $this->applyAllFilters ( $request, $query );

        $proyeks      = $this->getProyeks ();
        $TableData    = $this->getTableData ( $query, $perPage );
        $uniqueValues = $this->getUniqueValues ( $query, $proyeks );

        return view ( 'dashboard.masterdata.alat.alat', [ 
            'headerPage'   => "Master Data Alat",
            'page'         => 'Data Alat',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    private function getPerPage ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        return in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;
    }

    private function buildBaseQuery ( Request $request )
    {
        return MasterDataAlat::with ( 'proyekCurrent' )
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );
    }

    private function applySearchFilter ( Request $request, $query )
    {
        if ( $request->has ( 'search' ) && ! empty ( $request->get ( 'search' ) ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'jenis_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'kode_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'merek_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'tipe_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'serial_number', 'ilike', "%{$search}%" )
                    ->orWhereHas ( 'alatProyek', function ($sq) use ($search)
                    {
                        $sq->whereNull ( 'removed_at' )
                            ->whereHas ( 'proyek', function ($pq) use ($search)
                            {
                                $pq->where ( 'nama', 'ilike', "%{$search}%" );
                            } );
                    } )
                    ->orWhere ( function ($sq) use ($search)
                    {
                        if ( str_contains ( strtolower ( $search ), 'belum ditugaskan' ) )
                        {
                            $sq->whereDoesntHave ( 'alatProyek', function ($aq)
                            {
                                $aq->whereNull ( 'removed_at' );
                            } );
                        }
                    } );
            } );
        }
    }

    private function applyAllFilters ( Request $request, $query )
    {
        $this->handleJenisFilter ( $request, $query );
        $this->handleMerekFilter ( $request, $query );
        $this->handleKodeFilter ( $request, $query );
        $this->handleTipeFilter ( $request, $query );
        $this->handleSerialFilter ( $request, $query );
        $this->handleProyekFilter ( $request, $query );
    }

    private function getProyeks ()
    {
        $user         = Auth::user ();
        $proyeksQuery = Proyek::with ( "users" );

        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        return $proyeksQuery->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();
    }

    private function getTableData ( $query, $perPage )
    {
        return $query->paginate ( $perPage )->withQueryString ();
    }

    private function getUniqueValues ( $query, $proyeks )
    {
        $queryForUniqueValues = clone $query;
        $results              = $queryForUniqueValues->get ();

        // Get all active alat records with their current projects
        $activeAlatWithProjects = $results->map(function($alat) {
            return [
                'jenis_alat' => $alat->jenis_alat,
                'merek_alat' => $alat->merek_alat,
                'kode_alat' => $alat->kode_alat,
                'tipe_alat' => $alat->tipe_alat,
                'serial_number' => $alat->serial_number,
                'proyek_name' => $alat->current_project ? $alat->current_project->nama : null
            ];
        });

        // Get unique values
        $uniqueProyeks = collect($activeAlatWithProjects)
            ->pluck('proyek_name')
            ->filter()
            ->unique()
            ->values();

        return [ 
            'jenis'  => $results->pluck ( 'jenis_alat' )->unique ()->values (),
            'merek'  => $results->pluck ( 'merek_alat' )->unique ()->values (),
            'kode'   => $results->pluck ( 'kode_alat' )->unique ()->values (),
            'tipe'   => $results->pluck ( 'tipe_alat' )->unique ()->values (),
            'serial' => $results->pluck ( 'serial_number' )->unique ()->values (),
            'proyek' => $uniqueProyeks
        ];
    }

    private function handleJenisFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_jenis' ) )
        {
            $jenis = explode ( ',', $request->selected_jenis );
            $jenis = array_map ( function ($val)
            {
                return $val === 'null' ? $val : base64_decode ( $val );
            }, $jenis );
            if ( in_array ( 'null', $jenis ) )
            {
                $nonNullValues = array_filter ( $jenis, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'jenis_alat' )
                        ->orWhere ( 'jenis_alat', '-' )
                        ->orWhereIn ( 'jenis_alat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'jenis_alat', $jenis );
            }
        }
    }

    private function handleMerekFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_merek' ) )
        {
            $merek = explode ( ',', $request->selected_merek );
            $merek = array_map ( function ($val)
            {
                return $val === 'null' ? $val : base64_decode ( $val );
            }, $merek );
            if ( in_array ( 'null', $merek ) )
            {
                $nonNullValues = array_filter ( $merek, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'merek_alat' )
                        ->orWhere ( 'merek_alat', '-' )
                        ->orWhereIn ( 'merek_alat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'merek_alat', $merek );
            }
        }
    }

    private function handleKodeFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_kode' ) )
        {
            $kode = explode ( ',', $request->selected_kode );
            $kode = array_map ( function ($val)
            {
                return $val === 'null' ? $val : base64_decode ( $val );
            }, $kode );
            if ( in_array ( 'null', $kode ) )
            {
                $nonNullValues = array_filter ( $kode, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'kode_alat' )
                        ->orWhere ( 'kode_alat', '-' )
                        ->orWhereIn ( 'kode_alat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'kode_alat', $kode );
            }
        }
    }

    private function handleTipeFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_tipe' ) )
        {
            $tipe = explode ( ',', $request->selected_tipe );
            $tipe = array_map ( function ($val)
            {
                return $val === 'null' ? $val : base64_decode ( $val );
            }, $tipe );
            if ( in_array ( 'null', $tipe ) )
            {
                $nonNullValues = array_filter ( $tipe, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'tipe_alat' )
                        ->orWhere ( 'tipe_alat', '-' )
                        ->orWhereIn ( 'tipe_alat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'tipe_alat', $tipe );
            }
        }
    }

    private function handleSerialFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_serial' ) )
        {
            $serial = explode ( ',', $request->selected_serial );
            $serial = array_map ( function ($val)
            {
                return $val === 'null' ? $val : base64_decode ( $val );
            }, $serial );
            if ( in_array ( 'null', $serial ) )
            {
                $nonNullValues = array_filter ( $serial, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'serial_number' )
                        ->orWhere ( 'serial_number', '-' )
                        ->orWhereIn ( 'serial_number', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'serial_number', $serial );
            }
        }
    }

    private function handleProyekFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_proyek' ) )
        {
            $proyek = explode ( ',', $request->selected_proyek );
            $proyek = array_map ( function ($val)
            {
                return $val === 'null' ? $val : base64_decode ( $val );
            }, $proyek );
            if ( in_array ( 'null', $proyek ) )
            {
                $nonNullValues = array_filter ( $proyek, fn ( $value ) => $value !== 'null' );
                if ( empty ( $nonNullValues ) )
                {
                    $query->whereDoesntHave ( 'alatProyek', function ($q)
                    {
                        $q->whereNull ( 'removed_at' );
                    } );
                }
                else
                {
                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        $q->whereDoesntHave ( 'alatProyek', function ($sub)
                        {
                            $sub->whereNull ( 'removed_at' );
                        } )->orWhereHas ( 'alatProyek', function ($sub) use ($nonNullValues)
                        {
                            $sub->whereNull ( 'removed_at' )
                                ->whereHas ( 'proyek', function ($p) use ($nonNullValues)
                                {
                                    $p->whereIn ( 'nama', $nonNullValues );
                                } );
                        } );
                    } );
                }
            }
            else
            {
                $query->whereHas ( 'alatProyek', function ($q) use ($proyek)
                {
                    $q->whereNull ( 'removed_at' )
                        ->whereHas ( 'proyek', function ($p) use ($proyek)
                        {
                            $p->whereIn ( 'nama', $proyek );
                        } );
                } );
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store ( Request $request )
    {
        $validatedData = $request->validate ( [ 
            'jenis_alat'    => 'required|string',
            'kode_alat'     => 'required|string',
            'merek_alat'    => 'required|string',
            'tipe_alat'     => 'required|string',
            'serial_number' => 'required|string',
        ] );

        $alat = MasterDataAlat::create ( $validatedData );

        return redirect ()->route ( 'master_data_alat.index' )->with ( 'success', 'Master Data Alat berhasil ditambahkan' );
    }

    /**
     * Display the specified resource.
     */
    public function show ( $id )
    {
        $alat = MasterDataAlat::findOrFail ( $id );

        return response ()->json ( $alat );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update ( Request $request, $id )
    {
        $alat = MasterDataAlat::findOrFail ( $id );

        $validatedData = $request->validate ( [ 
            'jenis_alat'    => 'required|string',
            'kode_alat'     => 'required|string',
            'merek_alat'    => 'required|string',
            'tipe_alat'     => 'required|string',
            'serial_number' => 'required|string',
        ] );

        $alat->update ( $validatedData );

        return redirect ()->route ( 'master_data_alat.index' )->with ( 'success', 'Master Data Alat berhasil diperbarui' );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy ( $id )
    {
        $alat = MasterDataAlat::findOrFail ( $id );
        $alat->delete ();

        return redirect ()->route ( 'master_data_alat.index' )->with ( 'success', 'Master Data Alat berhasil dihapus' );
    }

    public function getHistory ( $id )
    {
        $alat = MasterDataAlat::findOrFail ( $id );
        return response ()->json (
            $alat->alatProyek ()
                ->with ( 'proyek' )
                ->orderBy ( 'assigned_at', 'desc' )
                ->get ()
        );
    }
}
