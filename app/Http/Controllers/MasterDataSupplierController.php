<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use App\Http\Controllers\Controller;

class MasterDataSupplierController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )->orderBy ( "updated_at", "asc" )->orderBy ( "id", "asc" )->get ();

        $spareparts = \App\Models\MasterDataSparepart::all ();

        return view ( 'dashboard.masterdata.supplier.supplier', [ 
            'proyek'     => $proyeks,
            'proyeks'    => $proyeks,
            'spareparts' => $spareparts,

            'headerPage' => "Master Data Supplier",
            'page'       => 'Data Supplier',
        ] );
    }

    public function getData ( Request $request )
    {
        // Query dari model MasterDataSupplier
        $query = MasterDataSupplier::query ();

        // Filter berdasarkan search term
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( 'nama', 'like', "%{$search}%" );
        }

        // Sorting berdasarkan permintaan dari DataTables
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

        // Handle pagination
        $start           = $request->input ( 'start', 0 );
        $length          = $request->input ( 'length', 10 );
        $totalRecords    = MasterDataSupplier::count ();
        $filteredRecords = $query->count ();

        // Ambil data dengan pagination
        $suppliers = $query->skip ( $start )->take ( $length )->get ( [ 'id', 'nama' ] );

        // Return data dalam format yang diterima oleh DataTables
        return response ()->json ( [ 
            'draw'            => $request->input ( 'draw' ),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $suppliers,
        ] );
    }

    public function show ( $id )
    {
        $supplier = MasterDataSupplier::with ( 'spareparts' )->findOrFail ( $id );

        return response ()->json ( [ 
            'data' => $supplier,
        ] );
    }

    public function store ( Request $request )
    {
        $validatedData = $request->validate ( [ 
            'nama'         => 'required|string|max:255',
            'spareparts.*' => 'exists:master_data_sparepart,id', // Pastikan hanya spareparts yang valid yang bisa dipilih
        ] );

        // Buat Supplier baru
        $supplier = MasterDataSupplier::create ( [ 
            'nama' => $validatedData[ 'nama' ],
        ] );

        // Lampirkan spareparts jika ada yang dipilih
        if ( ! empty ( $validatedData[ 'spareparts' ] ) )
        {
            $supplier->spareparts ()->attach ( $validatedData[ 'spareparts' ] );
        }

        return redirect ()->route ( 'master_data_supplier.index' )->with ( 'success', 'Master Data Supplier berhasil ditambahkan' );
    }

    public function update ( Request $request, $id )
    {
        // Validasi data request
        $validatedData = $request->validate ( [ 
            'nama'         => [ 'required', 'string', 'max:255' ],
            'spareparts'   => [ 'array' ], // Validasi bahwa sparepart adalah array
            'spareparts.*' => [ 'exists:master_data_sparepart,id' ], // Pastikan setiap sparepart ID valid
            // Tambahkan validasi lain jika diperlukan
        ] );

        // Temukan data supplier berdasarkan ID
        $supplier = MasterDataSupplier::findOrFail ( $id );

        // Perbarui data supplier menggunakan hasil validasi
        $supplier->update ( $request->only ( 'nama' ) );

        $supplier->spareparts ()->sync ( $request->input ( 'spareparts', [] ) );

        // Redirect kembali ke halaman indeks dengan pesan sukses
        return redirect ()->route ( 'master_data_supplier.index' )->with ( 'success', 'Master Data Supplier berhasil diubah' );
    }

    public function destroy ( $id )
    {
        $supplier = MasterDataSupplier::findOrFail ( $id );
        $supplier->delete ();

        return redirect ()->route ( 'master_data_supplier.index' )->with ( 'success', 'Master Data Supplier berhasil dihapus' );
    }
}
