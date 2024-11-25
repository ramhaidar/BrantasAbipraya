<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangAPI extends Controller
{
    public function store ( Request $request )
    {
        $request->validate ( [ 'id_barang' => 'required|max:255', 'nama_barang' => 'required|max:255', 'jenis_barang' => 'required|max:255', 'satuan_barang' => 'required|max:255', 'gudang' => 'required|max:255', 'stok_barang' => 'required|integer|gt:0',] );
        $barang                = new Barang;
        $barang->id_barang     = $request->id_barang;
        $barang->nama_barang   = $request->nama_barang;
        $barang->jenis_barang  = $request->jenis_barang;
        $barang->satuan_barang = $request->satuan_barang;
        $barang->gudang        = $request->gudang;
        $barang->stok_barang   = $request->stok_barang;
        $barang->save ();
        return response ()->json ( [ 'message' => 'Data barang berhasil disimpan', 'data' => $barang,] );
    }
    public function show ( $id )
    {
        $barang = Barang::find ( $id );
        if ( $barang )
        {
            return response ()->json ( [ 'message' => 'Data barang berhasil ditemukan', 'data' => $barang ] );
        }
        else
        {
            return response ()->json ( [ 'message' => 'Barang not found' ], 404 );
        }
    }
    public function update ( Request $request, $id )
    {
        $barang = Barang::find ( $id );
        if ( $barang )
        {
            $request->validate ( [ 'id_barang' => 'required|max:255', 'nama_barang' => 'required|max:255', 'jenis_barang' => 'required|max:255', 'satuan_barang' => 'required|max:255', 'gudang' => 'required|max:255', 'stok_barang' => 'required|integer|gt:0',] );
            $barang->id_barang     = $request->id_barang;
            $barang->nama_barang   = $request->nama_barang;
            $barang->jenis_barang  = $request->jenis_barang;
            $barang->satuan_barang = $request->satuan_barang;
            $barang->gudang        = $request->gudang;
            $barang->stok_barang   = $request->stok_barang;
            $barang->save ();
            return response ()->json ( [ 'message' => 'Data barang berhasil diubah', 'data' => $barang ] );
        }
        else
        {
            return response ()->json ( [ 'message' => 'Barang not found' ], 404 );
        }
    }
    public function destroy ( $id )
    {
        $barang = Barang::find ( $id );
        if ( $barang )
        {
            $barang->delete ();
            return response ()->json ( [ 'message' => 'Barang deleted successfully' ], 200 );
        }
        else
        {
            return response ()->json ( [ 'message' => 'Barang not found' ], 404 );
        }
    }
}