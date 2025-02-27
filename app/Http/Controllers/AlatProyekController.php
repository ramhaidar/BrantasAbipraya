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

    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];

        try
        {
            // Use special delimiter for consistency
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            \Log::error ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
    }

    private function applyFilters ( Request $request, $query )
    {
        $filterFields = [ 
            'jenis_alat'    => 'selected_jenis_alat',
            'kode_alat'     => 'selected_kode_alat',
            'merek_alat'    => 'selected_merek_alat',
            'tipe_alat'     => 'selected_tipe_alat',
            'serial_number' => 'selected_serial_number'
        ];

        foreach ( $filterFields as $field => $paramName )
        {
            if ( $request->filled ( $paramName ) )
            {
                $selectedValues = $this->getSelectedValues ( $request->get ( $paramName ) );

                if ( ! empty ( $selectedValues ) )
                {
                    $query->where ( function ($q) use ($field, $selectedValues)
                    {
                        $q->whereHas ( 'masterDataAlat', function ($subQ) use ($field, $selectedValues)
                        {
                            if ( in_array ( 'null', $selectedValues ) )
                            {
                                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                                $subQ->where ( function ($nullQ) use ($field, $nonNullValues)
                                {
                                    $nullQ->whereNull ( $field )
                                        ->orWhere ( $field, '-' )
                                        ->when ( ! empty ( $nonNullValues ), function ($q) use ($field, $nonNullValues)
                                        {
                                            $q->orWhereIn ( $field, $nonNullValues );
                                        } );
                                } );
                            }
                            else
                            {
                                $subQ->whereIn ( $field, $selectedValues );
                            }
                        } );
                    } );
                }
            }
        }
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
        // Get all master data alat IDs associated with this project
        $alatIds = AlatProyek::where ( 'id_proyek', $proyekId )
            ->whereNull ( 'removed_at' )
            ->pluck ( 'id_master_data_alat' );

        // Get all master data records for this project without any filtering
        $masterDataAlat = MasterDataAlat::whereIn ( 'id', $alatIds )->get ();

        // Return unique values for each field
        return [ 
            'jenis_alat'    => $masterDataAlat->pluck ( 'jenis_alat' )->filter ()->unique ()->values (),
            'kode_alat'     => $masterDataAlat->pluck ( 'kode_alat' )->filter ()->unique ()->values (),
            'merek_alat'    => $masterDataAlat->pluck ( 'merek_alat' )->filter ()->unique ()->values (),
            'tipe_alat'     => $masterDataAlat->pluck ( 'tipe_alat' )->filter ()->unique ()->values (),
            'serial_number' => $masterDataAlat->pluck ( 'serial_number' )->filter ()->unique ()->values (),
        ];
    }

    private function applyValueFilter ( $query, $field, $values )
    {
        if ( in_array ( 'null', $values ) )
        {
            $nonNullValues = array_filter ( $values, fn ( $v ) => $v !== 'null' );
            $query->where ( function ($q) use ($field, $nonNullValues)
            {
                $q->whereNull ( $field )
                    ->orWhere ( $field, '-' )
                    ->when ( ! empty ( $nonNullValues ), function ($sq) use ($field, $nonNullValues)
                    {
                        $sq->orWhereIn ( $field, $nonNullValues );
                    } );
            } );
        }
        else
        {
            $query->whereIn ( $field, $values );
        }
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
