<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Models\MasterDataSupplier;
use Illuminate\Support\Facades\Auth;

class MasterDataAlatController extends Controller
{
    public function index ( Request $request )
    {
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = MasterDataAlat::query ()
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'jenis_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'kode_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'merek_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'tipe_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'serial_number', 'like', "%{$search}%" );
            } );
        }

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $TableData = $query->paginate ( $perPage )
            ->withQueryString ();

        return view ( 'dashboard.masterdata.alat.alat', [ 
            'headerPage' => "Master Data Alat",
            'page'       => 'Data Alat',

            'proyeks'    => $proyeks,
            'TableData'  => $TableData,
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

    public function getData ( Request $request )
    {
        // Base query
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

            // Prevent ordering by columns that are not in the database
            if ( in_array ( $columnName, [ 'jenis_alat', 'kode_alat', 'merek_alat', 'tipe_alat', 'serial_number', 'updated_at' ] ) )
            {
                $query->orderBy ( $columnName, $sortDirection );
            }
        }
        else
        {
            $query->orderBy ( 'updated_at', 'desc' );
        }

        // Pagination parameters
        $draw   = $request->input ( 'draw' );
        $start  = $request->input ( 'start', 0 );
        $length = $request->input ( 'length', 10 );

        // Get total and filtered counts
        $totalRecords    = MasterDataAlat::count ();
        $filteredRecords = $query->count ();

        // Fetch the data with pagination
        $alat = $query->skip ( $start )->take ( $length )->get ();

        // Return response
        return response ()->json ( [ 
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $alat,
        ] );
    }

}
