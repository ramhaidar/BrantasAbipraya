<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MasterDataSupplierController extends Controller
{
    public function index ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = MasterDataSupplier::query ()
            ->with ( [ 'masterDataSpareparts' ] )
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nama', 'ilike', "%{$search}%" )
                    ->orWhere ( 'alamat', 'ilike', "%{$search}%" )
                    ->orWhere ( 'contact_person', 'ilike', "%{$search}%" )
                    ->orWhereHas ( 'masterDataSpareparts', function ($query) use ($search)
                    {
                        $query->where ( 'nama', 'ilike', "%{$search}%" )
                            ->orWhere ( 'part_number', 'ilike', "%{$search}%" )
                            ->orWhere ( 'merk', 'ilike', "%{$search}%" );
                    } );
            } );
        }

        $user = Auth::user ();

        if ( $user->role === 'Pegawai' )
        {
            $query->where ( 'id_user', $user->id );
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks       = $user->proyek ()
                ->with ( "users" )
                ->get ();
            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $query->whereIn ( 'id_user', $usersInProyek );
        }

        $suppliers = $query->paginate ( $perPage )
            ->withQueryString ();

        $spareparts = MasterDataSparepart::all ();

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $TableData = MasterDataSupplier::query ()
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        return view ( 'dashboard.masterdata.supplier.supplier', [ 
            'headerPage' => "Master Data Supplier",
            'page'       => 'Data Supplier',

            'proyeks'    => $proyeks,
            'TableData'  => $TableData,
            'suppliers'  => $suppliers,
            'spareparts' => $spareparts,
        ] );
    }

    public function show ( $id )
    {
        $supplier = MasterDataSupplier::with ( 'masterDataSpareparts' )->findOrFail ( $id );

        return response ()->json ( [ 
            'data' => $supplier,
        ] );
    }

    public function store ( Request $request )
    {
        // Validasi data yang diterima
        $validatedData = $request->validate ( [ 
            'nama'           => 'required|string|max:255',
            'alamat'         => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
            'spareparts'     => 'array', // Validasi bahwa spareparts adalah array
            'spareparts.*'   => 'exists:master_data_sparepart,id', // Pastikan spareparts yang dipilih valid
        ] );

        // Buat Supplier baru
        $supplier = MasterDataSupplier::create ( [ 
            'nama'           => $validatedData[ 'nama' ],
            'alamat'         => $validatedData[ 'alamat' ],
            'contact_person' => $validatedData[ 'contact_person' ],
        ] );

        // Lampirkan spareparts jika ada yang dipilih
        if ( ! empty ( $validatedData[ 'spareparts' ] ) )
        {
            $supplier->masterDataSpareparts ()->attach ( $validatedData[ 'spareparts' ] );
        }

        // Redirect ke halaman indeks dengan pesan sukses
        return redirect ()->route ( 'master_data_supplier.index' )
            ->with ( 'success', 'Master Data Supplier berhasil ditambahkan' );
    }

    public function update ( Request $request, $id )
    {
        // Validasi data request
        $validatedData = $request->validate ( [ 
            'nama'           => 'required|string|max:255',
            'alamat'         => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
            'spareparts'     => 'array', // Validasi bahwa spareparts adalah array
            'spareparts.*'   => 'exists:master_data_sparepart,id', // Pastikan setiap sparepart ID valid
        ] );

        // Temukan data supplier berdasarkan ID
        $supplier = MasterDataSupplier::findOrFail ( $id );

        // Perbarui data supplier menggunakan hasil validasi
        $supplier->update ( [ 
            'nama'           => $validatedData[ 'nama' ],
            'alamat'         => $validatedData[ 'alamat' ],
            'contact_person' => $validatedData[ 'contact_person' ],
        ] );

        // Sinkronisasi spareparts
        $supplier->masterDataSpareparts ()->sync ( $request->input ( 'spareparts', [] ) );

        // Redirect ke halaman indeks dengan pesan sukses
        return redirect ()->route ( 'master_data_supplier.index' )
            ->with ( 'success', 'Master Data Supplier berhasil diubah' );
    }

    public function destroy ( $id )
    {
        $supplier = MasterDataSupplier::findOrFail ( $id );
        $supplier->delete ();

        return redirect ()->route ( 'master_data_supplier.index' )->with ( 'success', 'Master Data Supplier berhasil dihapus' );
    }
}
