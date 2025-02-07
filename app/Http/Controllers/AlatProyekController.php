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
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $proyek = Proyek::with ( "users" )->findOrFail ( $request->id_proyek );

        $query = AlatProyek::query ()
            ->with ( 'masterDataAlat' )
            ->where ( 'id_proyek', $proyek->id )
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

        $this->handleJenisAlatFilter ( $request, $query );
        $this->handleKodeAlatFilter ( $request, $query );
        $this->handleMerekAlatFilter ( $request, $query );
        $this->handleTipeAlatFilter ( $request, $query );
        $this->handleSerialNumberFilter ( $request, $query );

        $TableData = $perPage === -1
            ? $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $query->count () )
            : $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' )
                ->paginate ( $perPage );

        $TableData = $TableData->withQueryString ();

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

        $AlatAvailable = MasterDataAlat::whereDoesntHave ( 'alatProyek', function ($query)
        {
            $query->whereNull ( 'removed_at' );
        } )->get ();

        $uniqueValues = [ 
            'jenis_alat'    => MasterDataAlat::whereNotNull ( 'jenis_alat' )->distinct ()->pluck ( 'jenis_alat' ),
            'kode_alat'     => MasterDataAlat::whereNotNull ( 'kode_alat' )->distinct ()->pluck ( 'kode_alat' ),
            'merek_alat'    => MasterDataAlat::whereNotNull ( 'merek_alat' )->distinct ()->pluck ( 'merek_alat' ),
            'tipe_alat'     => MasterDataAlat::whereNotNull ( 'tipe_alat' )->distinct ()->pluck ( 'tipe_alat' ),
            'serial_number' => MasterDataAlat::whereNotNull ( 'serial_number' )->distinct ()->pluck ( 'serial_number' ),
        ];

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

    private function handleJenisAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_jenis_alat' ) )
        {
            $jenisAlat = explode ( ',', $request->selected_jenis_alat );
            if ( in_array ( 'null', $jenisAlat ) )
            {
                $nonNullValues = array_filter ( $jenisAlat, fn ( $value ) => $value !== 'null' );
                $query->whereHas ( 'masterDataAlat', function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'jenis_alat' )
                        ->orWhere ( 'jenis_alat', '-' )
                        ->orWhereIn ( 'jenis_alat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataAlat', function ($q) use ($jenisAlat)
                {
                    $q->whereIn ( 'jenis_alat', $jenisAlat );
                } );
            }
        }
    }

    private function handleKodeAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_kode_alat' ) )
        {
            $kodeAlat = explode ( ',', $request->selected_kode_alat );
            if ( in_array ( 'null', $kodeAlat ) )
            {
                $nonNullValues = array_filter ( $kodeAlat, fn ( $value ) => $value !== 'null' );
                $query->whereHas ( 'masterDataAlat', function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'kode_alat' )
                        ->orWhere ( 'kode_alat', '-' )
                        ->orWhereIn ( 'kode_alat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataAlat', function ($q) use ($kodeAlat)
                {
                    $q->whereIn ( 'kode_alat', $kodeAlat );
                } );
            }
        }
    }

    private function handleMerekAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_merek_alat' ) )
        {
            $merekAlat = explode ( ',', $request->selected_merek_alat );
            if ( in_array ( 'null', $merekAlat ) )
            {
                $nonNullValues = array_filter ( $merekAlat, fn ( $value ) => $value !== 'null' );
                $query->whereHas ( 'masterDataAlat', function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'merek_alat' )
                        ->orWhere ( 'merek_alat', '-' )
                        ->orWhereIn ( 'merek_alat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataAlat', function ($q) use ($merekAlat)
                {
                    $q->whereIn ( 'merek_alat', $merekAlat );
                } );
            }
        }
    }

    private function handleTipeAlatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_tipe_alat' ) )
        {
            $tipeAlat = explode ( ',', $request->selected_tipe_alat );
            if ( in_array ( 'null', $tipeAlat ) )
            {
                $nonNullValues = array_filter ( $tipeAlat, fn ( $value ) => $value !== 'null' );
                $query->whereHas ( 'masterDataAlat', function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'tipe_alat' )
                        ->orWhere ( 'tipe_alat', '-' )
                        ->orWhereIn ( 'tipe_alat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataAlat', function ($q) use ($tipeAlat)
                {
                    $q->whereIn ( 'tipe_alat', $tipeAlat );
                } );
            }
        }
    }

    private function handleSerialNumberFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_serial_number' ) )
        {
            $serialNumber = explode ( ',', $request->selected_serial_number );
            if ( in_array ( 'null', $serialNumber ) )
            {
                $nonNullValues = array_filter ( $serialNumber, fn ( $value ) => $value !== 'null' );
                $query->whereHas ( 'masterDataAlat', function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'serial_number' )
                        ->orWhere ( 'serial_number', '-' )
                        ->orWhereIn ( 'serial_number', $nonNullValues );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataAlat', function ($q) use ($serialNumber)
                {
                    $q->whereIn ( 'serial_number', $serialNumber );
                } );
            }
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
            // Create new AlatProyek record
            AlatProyek::create ( [ 
                'id_master_data_alat' => $alatId,
                'id_proyek'           => $validatedData[ 'id_proyek' ],
                'assigned_at'         => now (),
            ] );

            // Update the current project in MasterDataAlat
            MasterDataAlat::where ( 'id', $alatId )->update ( [ 
                'id_proyek_current' => $validatedData[ 'id_proyek' ]
            ] );
        }

        return redirect ()->back ()->with ( 'success', 'Alat berhasil ditambahkan ke proyek' );
    }

    public function destroy ( $id )
    {
        $alatProyek = AlatProyek::findOrFail ( $id );

        // Set removed_at timestamp
        $alatProyek->update ( [ 
            'removed_at' => now ()
        ] );

        // Clear the current project from MasterDataAlat
        $alatProyek->masterDataAlat ()->update ( [ 
            'id_proyek_current' => null
        ] );

        return redirect ()->back ()->with ( 'success', 'Alat berhasil dihapus dari proyek' );
    }
}
