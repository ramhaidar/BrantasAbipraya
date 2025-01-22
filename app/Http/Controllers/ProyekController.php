<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\UserProyek;
use Illuminate\Http\Request;

class ProyekController extends Controller
{
    public function index ( Request $request )
    {
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = Proyek::query ()
            ->with ( 'users' )
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nama', 'like', "%{$search}%" );
            } );
        }

        $proyeks = $query->paginate ( $perPage )
            ->withQueryString ();

        $TableData = $query->paginate ( $perPage )
            ->withQueryString ();

        return view ( "dashboard.proyek.proyek", [ 
            "headerPage" => "Proyek",
            "page"       => "Data Proyek",

            "proyeks"    => $proyeks,
            "TableData"  => $TableData,
        ] );
    }

    public function show ( $id )
    {
        $proyek = Proyek::with ( 'users' )
            ->findOrFail ( $id );

        return response ()->json ( [ 
            'data' => $proyek,
        ] );
    }

    public function store ( Request $request )
    {
        $credentials = $request->validate ( [ 
            "nama" => "required",
        ] );
        Proyek::create ( $credentials );
        return back ()->with ( "success", "Berhasil menambahkan data proyek." );
    }

    public function update ( Request $request, Proyek $id )
    {
        $credentials = $request->validate ( [ 
            "nama" => "required",
        ] );
        $id->update ( $credentials );
        $id->save ();

        return back ()->with ( "success", "Berhasil mengubah data proyek" );
    }
    public function destroy ( Proyek $id )
    {
        $id->delete ();
        $msg = "Berhasil menghapus data proyek";

        return back ()->with ( "success", $msg );
    }

    public function getData ( Request $request )
    {
        // Base query
        $query = Proyek::with ( 'users' );

        // Handle search input
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( 'nama', 'like', "%{$search}%" );
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
            $query->orderBy ( 'updated_at', 'desc' )
                ->orderBy ( 'id', 'desc' );
        }

        // Pagination parameters
        $draw   = $request->input ( 'draw' );
        $start  = $request->input ( 'start', 0 );
        $length = $request->input ( 'length', 10 );

        // Get total records and filtered records
        $totalRecords    = Proyek::count ();
        $filteredRecords = $query->count ();

        // Fetch paginated data
        $proyeks = $query->skip ( $start )
            ->take ( $length )
            ->get ();

        // Return JSON response
        return response ()->json ( [ 
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $proyeks,
        ] );
    }
}
