<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Proyek;
use Illuminate\Http\Request;

class MasterDataSupplierController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();

        $alat = Alat::with ( 'proyek', 'user' )
            ->orderBy ( 'updated_at', 'desc' )
            ->get ();
        return view ( 'dashboard.masterdata.supplier.supplier', [ 
            'proyek'     => $proyeks,
            'alat'       => $alat,
            'headerPage' => "Master Data Supplier",
            'page'       => 'Data Supplier',
            'proyeks'    => $proyeks,
        ] );
    }

    public function getData ( Request $request )
    {
        // Query dasar untuk mengambil data Proyek
        $query = Proyek::query ();

        // Filter berdasarkan search term
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( 'nama_proyek', 'like', "%{$search}%" );
        }

        // Sorting
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];
            $query->orderBy ( $columnName, $sortDirection );
        }
        else
        {
            $query->orderBy ( 'created_at', 'asc' )->orderBy ( 'id', 'asc' ); // Default order
        }

        // Handle pagination
        $start           = $request->input ( 'start', 0 );
        $length          = $request->input ( 'length', 10 );
        $totalRecords    = Proyek::count (); // Total records without filtering
        $filteredRecords = $query->count (); // Total records after filtering

        // Apply pagination
        $proyeks = $query->skip ( $start )->take ( $length )->get ( [ 'id', 'nama_proyek' ] );

        // Return data dalam format DataTables
        return response ()->json ( [ 
            'draw'            => $request->input ( 'draw' ),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $proyeks,
        ] );
    }

}
