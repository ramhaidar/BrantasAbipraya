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

        $TableData     = $this->getTableData ( $query, $perPage );
        $proyeks       = $this->getProyeks ();
        $AlatAvailable = $this->getAlatAvailable ();
        $uniqueValues  = $this->getUniqueValues ();

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

    private function getUniqueValues ()
    {
        return [ 
            'jenis_alat'    => MasterDataAlat::whereNotNull ( 'jenis_alat' )->distinct ()->pluck ( 'jenis_alat' ),
            'kode_alat'     => MasterDataAlat::whereNotNull ( 'kode_alat' )->distinct ()->pluck ( 'kode_alat' ),
            'merek_alat'    => MasterDataAlat::whereNotNull ( 'merek_alat' )->distinct ()->pluck ( 'merek_alat' ),
            'tipe_alat'     => MasterDataAlat::whereNotNull ( 'tipe_alat' )->distinct ()->pluck ( 'tipe_alat' ),
            'serial_number' => MasterDataAlat::whereNotNull ( 'serial_number' )->distinct ()->pluck ( 'serial_number' ),
        ];
    }

    private function handleJenisAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_jenis_alat' ) )
        {
            $jenisAlat = explode ( ',', $request->selected_jenis_alat );
            $this->applyFilter ( $query, 'jenis_alat', $jenisAlat );
        }
    }

    private function handleKodeAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_kode_alat' ) )
        {
            $kodeAlat = explode ( ',', $request->selected_kode_alat );
            $this->applyFilter ( $query, 'kode_alat', $kodeAlat );
        }
    }

    private function handleMerekAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_merek_alat' ) )
        {
            $merekAlat = explode ( ',', $request->selected_merek_alat );
            $this->applyFilter ( $query, 'merek_alat', $merekAlat );
        }
    }

    private function handleTipeAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_tipe_alat' ) )
        {
            $tipeAlat = explode ( ',', $request->selected_tipe_alat );
            $this->applyFilter ( $query, 'tipe_alat', $tipeAlat );
        }
    }

    private function handleSerialNumberFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_serial_number' ) )
        {
            $serialNumber = explode ( ',', $request->selected_serial_number );
            $this->applyFilter ( $query, 'serial_number', $serialNumber );
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
