<?php

namespace App\Http\Controllers;

use App\Models\MasterData;  // Gunakan model MasterData
use App\Models\Proyek;      // Gunakan model Proyek
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterDataController extends Controller
{
    public function index ()
    {
        $user       = Auth::user ();
        $proyeks    = [];
        $masterData = [];
        if ( $user->role === 'Admin' )
        {
            $proyeks    = Proyek::with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            $masterData = MasterData::with ( 'atbs' )->orderBy ( 'updated_at', 'desc' )->get ();
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks    = $user->proyek ()->with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            $masterData = MasterData::with ( 'atbs' )->orderBy ( 'updated_at', 'desc' )->get ();
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $proyeks = $user->proyek ()->with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            // $masterData = MasterData::with ( 'atbs' )->where ( 'id_user', $user->id )->orderBy ( 'updated_at', 'desc' )->get ();
            $masterData = MasterData::with ( 'atbs' )->orderBy ( 'updated_at', 'desc' )->get ();
        }
        return view ( 'dashboard.master_data', [ 'proyek' => $proyeks, 'masterData' => $masterData, 'page' => 'Master Data', 'proyeks' => $proyeks,] );
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
}
