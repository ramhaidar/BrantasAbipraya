<?php

namespace App\Http\Controllers;

use App\Models\MasterData;  // Gunakan model MasterData
use App\Models\Proyek;      // Gunakan model Proyek
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterDataSparepartController extends Controller
{
    public function index ( Request $request )
    {
        $user       = Auth::user ();
        $proyeks    = [];
        $masterData = [];

        if ( $user->role === 'Admin' )
        {
            $proyeks    = Proyek::with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            $masterData = MasterData::with ( 'atbs' )->orderBy ( 'updated_at', 'desc' )->paginate ( $request->input ( 'length', 10 ) );
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks       = $user->proyek ()->with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $masterData    = MasterData::whereIn ( 'id_user', $usersInProyek )
                ->with ( 'atbs' )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $proyeks    = $user->proyek ()->with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            $masterData = MasterData::where ( 'id_user', $user->id )
                ->with ( 'atbs' )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }

        return view ( 'dashboard.masterdata.sparepart.sparepart', [ 
            'proyek'     => $proyeks,
            'masterData' => $masterData,
            'headerPage' => "Master Data Sparepart",
            'page'       => 'Data Sparepart',
            'proyeks'    => $proyeks,
        ] );
    }

    public function store ( Request $request )
    {
        $request->validate ( [ 
            'supplier'     => [ 'required', 'string', 'max:255' ],
            'sparepart'    => [ 'required', 'string', 'max:255' ],
            'part_number'  => [ 'required', 'string', 'max:255', 'unique:master_data' ],
            'buffer_stock' => [ 'required', 'integer', 'min:0' ],
        ] );

        $masterData               = new MasterData;
        $masterData->supplier     = $request->supplier;
        $masterData->sparepart    = $request->sparepart;
        $masterData->part_number  = $request->part_number;
        $masterData->buffer_stock = $request->buffer_stock;
        $masterData->id_user      = Auth::id (); // Menyimpan ID user yang menambahkan data master
        $masterData->save ();

        return back ()->with ( 'success', 'Data Master berhasil ditambahkan' );
    }

    // Fungsi untuk mendapatkan data MasterData berdasarkan ID (untuk keperluan edit)
    public function show ( $id )
    {
        $masterData = MasterData::findOrFail ( $id );
        return response ()->json ( [ 'data' => $masterData ] );
    }

    // Fungsi untuk memperbarui data MasterData
    public function update ( Request $request, $id )
    {
        $request->validate ( [ 
            'supplier'     => [ 'required', 'string', 'max:255' ],
            'sparepart'    => [ 'required', 'string', 'max:255' ],
            'part_number'  => [ 'required', 'string', 'max:255', "unique:master_data,part_number,{$id}" ],
            'buffer_stock' => [ 'required', 'integer', 'min:0' ],
        ] );

        $masterData = MasterData::findOrFail ( $id );
        $masterData->update ( $request->all () );

        return back ()->with ( 'success', 'Data Master berhasil diperbarui' );
    }

    // Fungsi untuk menghapus data MasterData
    public function destroy ( $id )
    {
        $masterData = MasterData::findOrFail ( $id );
        $masterData->delete ();

        return response ()->json ( [ 'success' => 'Data berhasil dihapus' ] );
    }

    public function getData ( Request $request )
    {
        $query = MasterData::query ()->with ( 'atbs', 'user' ); // Pastikan relasi 'user' ada pada model

        // Filter berdasarkan search term
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'sparepart', 'like', "%{$search}%" )
                    ->orWhere ( 'part_number', 'like', "%{$search}%" )
                    ->orWhere ( 'buffer_stock', 'like', "%{$search}%" );
            } );
        }

        // Sorting
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex = $order[ 0 ][ 'column' ];
            $columnName  = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];

            // Ensure column name is valid to prevent SQL injection
            $sortableColumns = [ 'sparepart', 'part_number', 'buffer_stock', 'id_supplier', 'id_user' ];
            if ( in_array ( $columnName, $sortableColumns ) )
            {
                $sortDirection = $order[ 0 ][ 'dir' ];
                $query->orderBy ( $columnName, $sortDirection );
            }
        }
        else
        {
            $query->orderBy ( 'updated_at', 'desc' ); // Default order
        }

        // Handle pagination
        $start           = $request->input ( 'start', 0 );
        $length          = $request->input ( 'length', 10 );
        $totalRecords    = MasterData::count (); // Total records without filtering
        $filteredRecords = $query->count (); // Total records after filtering

        // Apply pagination
        $masterData = $query->skip ( $start )->take ( $length )->get ();

        return response ()->json ( [ 
            'draw'            => $request->input ( 'draw' ),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $masterData,
        ] );
    }
}
