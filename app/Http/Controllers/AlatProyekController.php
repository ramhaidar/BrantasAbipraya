<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AlatProyek;

class AlatProyekController extends Controller
{
    public function index ( Request $request )
    {
        $perPage = $this->getPerPage ( $request );
        $proyek  = Proyek::with ( "users" )->findOrFail ( $request->id_proyek );
        $query   = $this->buildQuery ( $request, $proyek->id );

        // Pass request to getUniqueValues
        $uniqueValues = $this->getUniqueValues ( $proyek->id, $request );

        $TableData     = $this->getTableData ( $query, $perPage );
        $proyeks       = $this->getProyeks ();
        $AlatAvailable = $this->getAlatAvailable ();

        return view ( 'dashboard.alat.alat', [ 
            'proyeks'       => $proyeks,
            'proyek'        => $proyek,
            'TableData'     => $TableData,
            'AlatAvailable' => $AlatAvailable,
            'headerPage'    => "Data Alat Proyek",
            'page'          => 'Data Alat',
            'uniqueValues'  => $uniqueValues,
        ] );
    }

    private function getPerPage ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        return in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;
    }

    private function buildQuery ( Request $request, $proyekId )
    {
        $query = AlatProyek::query ()
            ->with ( 'masterDataAlat' )
            ->where ( 'id_proyek', $proyekId )
            ->whereNull ( 'removed_at' );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->whereHas ( 'masterDataAlat', function ($q) use ($search)
            {
                $q->where ( 'jenis_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'kode_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'merek_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'tipe_alat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'serial_number', 'ilike', "%{$search}%" );
            } );
        }

        $this->applyFilters ( $request, $query );

        return $query;
    }

    private function applyFilters ( Request $request, $query )
    {
        $this->handleJenisAlatFilter ( $request, $query );
        $this->handleKodeAlatFilter ( $request, $query );
        $this->handleMerekAlatFilter ( $request, $query );
        $this->handleTipeAlatFilter ( $request, $query );
        $this->handleSerialNumberFilter ( $request, $query );
    }

    private function getTableData ( $query, $perPage )
    {
        return $perPage === -1
            ? $query->orderBy ( 'updated_at', 'desc' )->orderBy ( 'id', 'desc' )->paginate ( $query->count () )
            : $query->orderBy ( 'updated_at', 'desc' )->orderBy ( 'id', 'desc' )->paginate ( $perPage )->withQueryString ();
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

        return $proyeksQuery->orderBy ( "updated_at", "desc" )->orderBy ( "id", "desc" )->get ();
    }

    private function getAlatAvailable ()
    {
        return MasterDataAlat::whereDoesntHave ( 'alatProyek', function ($query)
        {
            $query->whereNull ( 'removed_at' );
        } )->get ();
    }

    private function getUniqueValues ( $proyekId, Request $request = null )
    {
        // Base query for alat proyek with the specified project
        $baseQuery = AlatProyek::where ( 'id_proyek', $proyekId )
            ->whereNull ( 'removed_at' );

        // Get the base set of master data alat IDs for this project
        $masterDataAlatIds = $baseQuery->pluck ( 'id_master_data_alat' );

        // Initialize separate queries for each filter
        $queries = [ 
            'jenis_alat'    => MasterDataAlat::whereIn ( 'id', $masterDataAlatIds ),
            'kode_alat'     => MasterDataAlat::whereIn ( 'id', $masterDataAlatIds ),
            'merek_alat'    => MasterDataAlat::whereIn ( 'id', $masterDataAlatIds ),
            'tipe_alat'     => MasterDataAlat::whereIn ( 'id', $masterDataAlatIds ),
            'serial_number' => MasterDataAlat::whereIn ( 'id', $masterDataAlatIds )
        ];

        if ( $request )
        {
            // Handle each filter separately and apply to ALL queries including the current field's query
            if ( $request->filled ( 'selected_jenis_alat' ) )
            {
                $values = $this->decodeBase64Filter ( $request->selected_jenis_alat );
                foreach ( $queries as $query )
                {
                    $query->whereIn ( 'jenis_alat', array_filter ( $values, fn ( $v ) => $v !== 'null' ) )
                        ->when ( in_array ( 'null', $values ), function ($q)
                        {
                            $q->orWhereNull ( 'jenis_alat' )
                                ->orWhere ( 'jenis_alat', '-' );
                        } );
                }
            }

            if ( $request->filled ( 'selected_kode_alat' ) )
            {
                $values = $this->decodeBase64Filter ( $request->selected_kode_alat );
                foreach ( $queries as $query )
                {
                    $query->whereIn ( 'kode_alat', array_filter ( $values, fn ( $v ) => $v !== 'null' ) )
                        ->when ( in_array ( 'null', $values ), function ($q)
                        {
                            $q->orWhereNull ( 'kode_alat' )
                                ->orWhere ( 'kode_alat', '-' );
                        } );
                }
            }

            if ( $request->filled ( 'selected_merek_alat' ) )
            {
                $values = $this->decodeBase64Filter ( $request->selected_merek_alat );
                foreach ( $queries as $query )
                {
                    $query->whereIn ( 'merek_alat', array_filter ( $values, fn ( $v ) => $v !== 'null' ) )
                        ->when ( in_array ( 'null', $values ), function ($q)
                        {
                            $q->orWhereNull ( 'merek_alat' )
                                ->orWhere ( 'merek_alat', '-' );
                        } );
                }
            }

            if ( $request->filled ( 'selected_tipe_alat' ) )
            {
                $values = $this->decodeBase64Filter ( $request->selected_tipe_alat );
                foreach ( $queries as $query )
                {
                    $query->whereIn ( 'tipe_alat', array_filter ( $values, fn ( $v ) => $v !== 'null' ) )
                        ->when ( in_array ( 'null', $values ), function ($q)
                        {
                            $q->orWhereNull ( 'tipe_alat' )
                                ->orWhere ( 'tipe_alat', '-' );
                        } );
                }
            }

            if ( $request->filled ( 'selected_serial_number' ) )
            {
                $values = $this->decodeBase64Filter ( $request->selected_serial_number );
                foreach ( $queries as $query )
                {
                    $query->whereIn ( 'serial_number', array_filter ( $values, fn ( $v ) => $v !== 'null' ) )
                        ->when ( in_array ( 'null', $values ), function ($q)
                        {
                            $q->orWhereNull ( 'serial_number' )
                                ->orWhere ( 'serial_number', '-' );
                        } );
                }
            }
        }

        // Get distinct values for each field
        return [ 
            'jenis_alat'    => $queries[ 'jenis_alat' ]->whereNotNull ( 'jenis_alat' )->distinct ()->pluck ( 'jenis_alat' ),
            'kode_alat'     => $queries[ 'kode_alat' ]->whereNotNull ( 'kode_alat' )->distinct ()->pluck ( 'kode_alat' ),
            'merek_alat'    => $queries[ 'merek_alat' ]->whereNotNull ( 'merek_alat' )->distinct ()->pluck ( 'merek_alat' ),
            'tipe_alat'     => $queries[ 'tipe_alat' ]->whereNotNull ( 'tipe_alat' )->distinct ()->pluck ( 'tipe_alat' ),
            'serial_number' => $queries[ 'serial_number' ]->whereNotNull ( 'serial_number' )->distinct ()->pluck ( 'serial_number' ),
        ];
    }

    private function decodeBase64Filter ( $encodedValue )
    {
        if ( ! $encodedValue ) return [];
        try
        {
            $decoded = base64_decode ( $encodedValue );
            return $decoded ? explode ( ',', $decoded ) : [];
        }
        catch ( \Exception $e )
        {
            return [];
        }
    }

    private function handleJenisAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_jenis_alat' ) )
        {
            $jenisAlat = $this->decodeBase64Filter ( $request->selected_jenis_alat );
            if ( ! empty ( $jenisAlat ) )
            {
                $this->applyFilter ( $query, 'jenis_alat', $jenisAlat );
            }
        }
    }

    private function handleKodeAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_kode_alat' ) )
        {
            $kodeAlat = $this->decodeBase64Filter ( $request->selected_kode_alat );
            if ( ! empty ( $kodeAlat ) )
            {
                $this->applyFilter ( $query, 'kode_alat', $kodeAlat );
            }
        }
    }

    private function handleMerekAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_merek_alat' ) )
        {
            $merekAlat = $this->decodeBase64Filter ( $request->selected_merek_alat );
            if ( ! empty ( $merekAlat ) )
            {
                $this->applyFilter ( $query, 'merek_alat', $merekAlat );
            }
        }
    }

    private function handleTipeAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_tipe_alat' ) )
        {
            $tipeAlat = $this->decodeBase64Filter ( $request->selected_tipe_alat );
            if ( ! empty ( $tipeAlat ) )
            {
                $this->applyFilter ( $query, 'tipe_alat', $tipeAlat );
            }
        }
    }

    private function handleSerialNumberFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_serial_number' ) )
        {
            $serialNumber = $this->decodeBase64Filter ( $request->selected_serial_number );
            if ( ! empty ( $serialNumber ) )
            {
                $this->applyFilter ( $query, 'serial_number', $serialNumber );
            }
        }
    }

    private function applyFilter ( $query, $field, $values )
    {
        if ( in_array ( 'null', $values ) )
        {
            $nonNullValues = array_filter ( $values, fn ( $value ) => $value !== 'null' );
            $query->whereHas ( 'masterDataAlat', function ($q) use ($field, $nonNullValues)
            {
                $q->whereNull ( $field )
                    ->orWhere ( $field, '-' )
                    ->orWhereIn ( $field, $nonNullValues );
            } );
        }
        else
        {
            $query->whereHas ( 'masterDataAlat', function ($q) use ($field, $values)
            {
                $q->whereIn ( $field, $values );
            } );
        }
    }

    private function applyFilterToQuery ( $query, $field, $values )
    {
        if ( in_array ( 'null', $values ) )
        {
            $nonNullValues = array_filter ( $values, fn ( $value ) => $value !== 'null' );
            $query->where ( function ($q) use ($field, $nonNullValues)
            {
                $q->whereNull ( $field )
                    ->orWhere ( $field, '-' )
                    ->orWhereIn ( $field, $nonNullValues );
            } );
        }
        else
        {
            $query->whereIn ( $field, $values );
        }
        return $query;
    }

    public function store ( Request $request )
    {
        $validatedData = $request->validate ( [ 
            'id_master_data_alat'   => 'required|array',
            'id_master_data_alat.*' => 'exists:master_data_alat,id',
            'id_proyek'             => 'required|exists:proyek,id',
        ] );

        foreach ( $validatedData[ 'id_master_data_alat' ] as $alatId )
        {
            AlatProyek::create ( [ 
                'id_master_data_alat' => $alatId,
                'id_proyek'           => $validatedData[ 'id_proyek' ],
                'assigned_at'         => now (),
            ] );

            MasterDataAlat::where ( 'id', $alatId )->update ( [ 
                'id_proyek_current' => $validatedData[ 'id_proyek' ]
            ] );
        }

        return redirect ()->back ()->with ( 'success', 'Alat berhasil ditambahkan ke proyek' );
    }

    public function destroy ( $id )
    {
        $alatProyek = AlatProyek::findOrFail ( $id );

        $alatProyek->update ( [ 
            'removed_at' => now ()
        ] );

        $alatProyek->masterDataAlat ()->update ( [ 
            'id_proyek_current' => null
        ] );

        return redirect ()->back ()->with ( 'success', 'Alat berhasil dihapus dari proyek' );
    }
}
