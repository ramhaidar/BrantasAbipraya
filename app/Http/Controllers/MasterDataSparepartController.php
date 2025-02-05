<?php

namespace App\Http\Controllers;

use App\Models\KategoriSparepart;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MasterDataSparepartController extends Controller
{
    public function index ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $query = MasterDataSparepart::query ()
            ->with ( [ 'kategoriSparepart', 'masterDataSuppliers' ] )
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nama', 'ilike', "%{$search}%" )
                    ->orWhere ( 'part_number', 'ilike', "%{$search}%" )
                    ->orWhere ( 'merk', 'ilike', "%{$search}%" )
                    ->orWhereHas ( 'kategoriSparepart', function ($query) use ($search)
                    {
                        $query->where ( function ($q) use ($search)
                        {
                            $q->where ( 'kode', 'ilike', "%{$search}%" )
                                ->orWhere ( 'nama', 'ilike', "%{$search}%" )
                                ->orWhere ( 'jenis', 'ilike', "%{$search}%" )
                                ->orWhere ( 'sub_jenis', 'ilike', "%{$search}%" );
                        } );
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

        $suppliers = MasterDataSupplier::all ();
        $kategori  = KategoriSparepart::all ();

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $TableData = MasterDataSparepart::with ( [ 'kategoriSparepart', 'masterDataSuppliers' ] )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        return view ( 'dashboard.masterdata.sparepart.sparepart', [ 
            'headerPage' => "Master Data Sparepart",
            'page'       => 'Data Sparepart',

            'proyeks'    => $proyeks,
            'TableData'  => $TableData,
            'suppliers'  => $suppliers,
            'categories' => $kategori,
        ] );
    }

    public function store ( Request $request )
    {
        $request->validate ( [ 
            'nama'        => [ 'required', 'string', 'max:255' ],
            'part_number' => [ 'required', 'string', 'max:255' ],
            'merk'        => [ 'required', 'string', 'max:255' ],
            'kategori'    => [ 'required', 'exists:kategori_sparepart,id' ], // Validasi id_kategori_sparepart
            'suppliers'   => [ 'array' ], // Validasi bahwa suppliers adalah array
            'suppliers.*' => [ 'exists:master_data_supplier,id' ], // Pastikan setiap supplier ID valid
        ] );

        // Simpan data utama MasterDataSparepart
        $sparepart                        = new MasterDataSparepart;
        $sparepart->nama                  = $request->input ( 'nama' );
        $sparepart->part_number           = $request->input ( 'part_number' );
        $sparepart->merk                  = $request->input ( 'merk' );
        $sparepart->id_kategori_sparepart = $request->input ( 'kategori' );
        $sparepart->save ();

        // Sinkronisasi suppliers menggunakan relasi many-to-many
        $sparepart->masterDataSuppliers ()->sync ( $request->input ( 'suppliers', [] ) );

        return redirect ()->route ( 'master_data_sparepart.index' )
            ->with ( 'success', 'Data Master Sparepart berhasil ditambahkan' );
    }

    public function show ( $id )
    {
        $sparepart = MasterDataSparepart::with ( 'masterDataSuppliers', 'kategoriSparepart' )->findOrFail ( $id );

        return response ()->json ( [ 
            'data' => [ 
                'id'                    => $sparepart->id,
                'nama'                  => $sparepart->nama,
                'part_number'           => $sparepart->part_number,
                'merk'                  => $sparepart->merk,
                'id_kategori_sparepart' => optional ( $sparepart->kategoriSparepart )->id, // Include ID kategori
                'kategori'              => $sparepart->kategoriSparepart ? [ 
                    'nama'      => $sparepart->kategoriSparepart->nama,
                    'kode'      => $sparepart->kategoriSparepart->kode,
                    'jenis'     => $sparepart->kategoriSparepart->jenis,
                    'sub_jenis' => $sparepart->kategoriSparepart->sub_jenis,
                ] : null,
                'suppliers'             => $sparepart->masterDataSuppliers->map ( function ($supplier)
                {
                    return [ 'id' => $supplier->id, 'nama' => $supplier->nama ];
                } ),
            ]
        ] );
    }

    public function update ( Request $request, $id )
    {
        $request->validate ( [ 
            'nama'        => [ 'required', 'string', 'max:255' ],
            'part_number' => [ 'required', 'string', 'max:255' ],
            'merk'        => [ 'required', 'string', 'max:255' ],
            'kategori'    => [ 'required', 'exists:kategori_sparepart,id' ], // Validasi id_kategori_sparepart
            'suppliers'   => [ 'array' ],
            'suppliers.*' => [ 'exists:master_data_supplier,id' ],
        ] );

        // Temukan sparepart berdasarkan ID
        $sparepart = MasterDataSparepart::findOrFail ( $id );

        // Update data sparepart
        $sparepart->update ( $request->only ( [ 'nama', 'part_number', 'merk', 'kategori' ] ) );

        // Update id_kategori_sparepart
        $sparepart->id_kategori_sparepart = $request->input ( 'kategori' );
        $sparepart->save ();

        // Sync suppliers, even if empty
        $sparepart->masterDataSuppliers ()->sync ( $request->input ( 'suppliers', [] ) );

        return redirect ()->route ( 'master_data_sparepart.index' )->with ( 'success', 'Master Data Sparepart berhasil diperbarui' );
    }

    public function destroy ( $id )
    {
        $sparepart = MasterDataSparepart::findOrFail ( $id );
        $sparepart->delete ();

        return redirect ()->route ( 'master_data_sparepart.index' )->with ( 'success', 'Master Data Sparepart berhasil dihapus' );
    }

    public function getSparepartsByCategory ( $id )
    {
        $spareparts = MasterDataSparepart::where ( 'id_kategori_sparepart', $id )->get ();

        return response ()->json ( $spareparts );
    }

    public function getSparepartsBySupplierAndCategory ( $supplier_id, $kategori_id )
    {
        $spareparts = MasterDataSparepart::with ( 'masterDataSuppliers' )
            ->whereHas ( 'masterDataSuppliers', function ($query) use ($supplier_id)
            {
                $query->where ( 'master_data_supplier.id', $supplier_id );
            } )
            ->where ( 'id_kategori_sparepart', $kategori_id )
            ->get ();

        return response ()->json ( $spareparts );
    }
}
