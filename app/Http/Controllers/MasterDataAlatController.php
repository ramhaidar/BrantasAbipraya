<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Models\MasterDataSupplier;
use Illuminate\Support\Facades\Auth;

class MasterDataAlatController extends Controller
{
    public function render ( Request $request )
    {
        $user    = Auth::user ();
        $proyeks = [];
        $alat    = [];

        if ( $user->role === 'Admin' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $alat = MasterDataAlat::orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $alat          = MasterDataAlat::whereIn ( 'id_user', $usersInProyek )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $alat = MasterDataAlat::where ( 'id_user', $user->id )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }
        return view ( 'dashboard.masterdata.alat.alat', [ 
            'proyeks'    => $proyeks,
            'proyek'     => $proyeks,
            'alat'       => $alat,

            'headerPage' => "Master Data Alat",
            'page'       => 'Data Alat',
        ] );
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

        return redirect ()->route ( 'master_data_alat' )->with ( 'success', 'Master Data Alat berhasil ditambahkan' );
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
     * Show the form for editing the specified resource.
     */
    public function edit ( $id )
    {
        $alat = MasterDataAlat::findOrFail ( $id );

        return view ( 'dashboard.masterdata.alat.edit', compact ( 'alat' ) );
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

        return redirect ()->route ( 'master_data_alat' )->with ( 'success', 'Master Data Alat berhasil diperbarui' );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy ( $id )
    {
        $alat = MasterDataAlat::findOrFail ( $id );
        $alat->delete ();

        return redirect ()->route ( 'master_data_alat' )->with ( 'success', 'Master Data Alat berhasil dihapus' );
    }

    public function getData ( Request $request )
    {
        $query = MasterDataAlat::query ();

        // Handle search input
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'jenis_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'kode_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'merek_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'tipe_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'serial_number', 'like', "%{$search}%" );
            } );
        }

        // Handle ordering
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];
            $query->orderBy ( $columnName, $sortDirection );
        }
        else
        {
            $query->orderBy ( 'updated_at', 'desc' );
        }

        // Calculate pagination parameters
        $page   = $request->input ( 'page', 1 );
        $length = $request->input ( 'length', 10 );
        $start  = ( $page - 1 ) * $length;

        // Get total records count and filtered count
        $totalRecords    = MasterDataAlat::count ();
        $filteredRecords = $query->count ();

        // Fetch the data with pagination
        $alat = $query->skip ( $start )->take ( $length )->get ();

        return response ()->json ( [ 
            'draw'            => $request->input ( 'draw' ),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $alat,
        ] );
    }

}
