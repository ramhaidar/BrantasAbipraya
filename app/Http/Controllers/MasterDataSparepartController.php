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

        $this->handleNamaFilter ( $request, $query );
        $this->handlePartNumberFilter ( $request, $query );
        $this->handleMerkFilter ( $request, $query );
        $this->handleKodeFilter ( $request, $query );
        $this->handleJenisFilter ( $request, $query );
        $this->handleSubJenisFilter ( $request, $query );
        $this->handleKategoriFilter ( $request, $query );

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

        $user         = Auth::user ();
        $proyeksQuery = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        $proyeks = $proyeksQuery
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        // Clone the query before pagination
        $queryForUniqueValues = clone $query;

        // Get unique values from the filtered query
        $uniqueValues = [ 
            'nama'        => $queryForUniqueValues->get ()->pluck ( 'nama' )->unique ()->values (),
            'part_number' => $queryForUniqueValues->get ()->pluck ( 'part_number' )->unique ()->values (),
            'merk'        => $queryForUniqueValues->get ()->pluck ( 'merk' )->unique ()->values (),
            'kode'        => KategoriSparepart::whereIn ( 'id', $queryForUniqueValues->get ()->pluck ( 'id_kategori_sparepart' ) )
                ->pluck ( 'kode' )->unique ()->values (),
            'jenis'       => KategoriSparepart::whereIn ( 'id', $queryForUniqueValues->get ()->pluck ( 'id_kategori_sparepart' ) )
                ->pluck ( 'jenis' )->unique ()->sort ()->values (),
            'sub_jenis'   => KategoriSparepart::whereIn ( 'id', $queryForUniqueValues->get ()->pluck ( 'id_kategori_sparepart' ) )
                ->pluck ( 'sub_jenis' )
                ->unique ()  // Hapus filter() agar nilai null tetap ada
                ->sort ()
                ->values (),
            'kategori'    => KategoriSparepart::whereIn ( 'id', $queryForUniqueValues->get ()->pluck ( 'id_kategori_sparepart' ) )
                ->pluck ( 'nama' )->unique ()->values (),
        ];

        $TableData = $query->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        return view ( 'dashboard.masterdata.sparepart.sparepart', [ 
            'headerPage'   => "Master Data Sparepart",
            'page'         => 'Data Sparepart',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'suppliers'    => $suppliers,
            'categories'   => $kategori,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    private function handleNamaFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_nama' ) )
        {
            $nama = explode ( ',', $request->selected_nama );
            if ( in_array ( 'null', $nama ) )
            {
                $nonNullValues = array_filter ( $nama, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'nama' )
                        ->orWhere ( 'nama', '-' )
                        ->orWhereIn ( 'nama', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'nama', $nama );
            }
        }
    }

    private function handlePartNumberFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_part_number' ) )
        {
            $partNumber = explode ( ',', $request->selected_part_number );
            if ( in_array ( 'null', $partNumber ) )
            {
                $nonNullValues = array_filter ( $partNumber, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'part_number' )
                        ->orWhere ( 'part_number', '-' )
                        ->orWhereIn ( 'part_number', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'part_number', $partNumber );
            }
        }
    }

    private function handleMerkFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_merk' ) )
        {
            $merk = explode ( ',', $request->selected_merk );
            if ( in_array ( 'null', $merk ) )
            {
                $nonNullValues = array_filter ( $merk, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'merk' )
                        ->orWhere ( 'merk', '-' )
                        ->orWhereIn ( 'merk', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'merk', $merk );
            }
        }
    }

    private function handleKategoriFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_kategori' ) )
        {
            $kategori = explode ( ',', $request->selected_kategori );
            if ( in_array ( 'null', $kategori ) )
            {
                $nonNullValues = array_filter ( $kategori, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereDoesntHave ( 'kategoriSparepart' )
                        ->orWhereHas ( 'kategoriSparepart', function ($sq) use ($nonNullValues)
                        {
                            $sq->whereIn ( 'nama', $nonNullValues );
                        } );
                } );
            }
            else
            {
                $query->whereHas ( 'kategoriSparepart', function ($q) use ($kategori)
                {
                    $q->whereIn ( 'nama', $kategori );
                } );
            }
        }
    }

    private function handleKodeFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_kode' ) )
        {
            $kode = explode ( ',', $request->selected_kode );
            if ( in_array ( 'null', $kode ) )
            {
                $nonNullValues = array_filter ( $kode, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereDoesntHave ( 'kategoriSparepart' )
                        ->orWhereHas ( 'kategoriSparepart', function ($sq) use ($nonNullValues)
                        {
                            $sq->whereIn ( 'kode', $nonNullValues );
                        } );
                } );
            }
            else
            {
                $query->whereHas ( 'kategoriSparepart', function ($q) use ($kode)
                {
                    $q->whereIn ( 'kode', $kode );
                } );
            }
        }
    }

    private function handleJenisFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_jenis' ) )
        {
            $jenis = explode ( ',', $request->selected_jenis );
            if ( in_array ( 'null', $jenis ) )
            {
                $nonNullValues = array_filter ( $jenis, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereDoesntHave ( 'kategoriSparepart' )
                        ->orWhereHas ( 'kategoriSparepart', function ($sq) use ($nonNullValues)
                        {
                            $sq->whereIn ( 'jenis', $nonNullValues );
                        } );
                } );
            }
            else
            {
                $query->whereHas ( 'kategoriSparepart', function ($q) use ($jenis)
                {
                    $q->whereIn ( 'jenis', $jenis );
                } );
            }
        }
    }

    private function handleSubJenisFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_sub_jenis' ) )
        {
            $subJenis = explode ( ',', $request->selected_sub_jenis );
            if ( in_array ( 'null', $subJenis ) )
            {
                $nonNullValues = array_filter ( $subJenis, fn ( $value ) => $value !== 'null' );
                $query->whereHas ( 'kategoriSparepart', function ($q) use ($nonNullValues)
                {
                    if ( ! empty ( $nonNullValues ) )
                    {
                        $q->whereNull ( 'sub_jenis' )->orWhereIn ( 'sub_jenis', $nonNullValues );
                    }
                    else
                    {
                        $q->whereNull ( 'sub_jenis' );
                    }
                } );
            }
            else
            {
                $query->whereHas ( 'kategoriSparepart', function ($q) use ($subJenis)
                {
                    $q->whereIn ( 'sub_jenis', $subJenis );
                } );
            }
        }
    }

    public function store ( Request $request )
    {
        $request->validate ( [ 
            'nama'        => [ 'required', 'string', 'max:255' ],
            'part_number' => [ 'required', 'string', 'max:255' ],
            'merk'        => [ 'required', 'string', 'max:255' ],
            'kategori'    => [ 'required', 'exists:kategori_sparepart,id' ],
            'suppliers'   => [ 'array' ],
            'suppliers.*' => [ 'exists:master_data_supplier,id' ],
        ] );

        $sparepart                        = new MasterDataSparepart;
        $sparepart->nama                  = $request->input ( 'nama' );
        $sparepart->part_number           = $request->input ( 'part_number' );
        $sparepart->merk                  = $request->input ( 'merk' );
        $sparepart->id_kategori_sparepart = $request->input ( 'kategori' );
        $sparepart->save ();

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
                'id_kategori_sparepart' => optional ( $sparepart->kategoriSparepart )->id,
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
            'kategori'    => [ 'required', 'exists:kategori_sparepart,id' ],
            'suppliers'   => [ 'array' ],
            'suppliers.*' => [ 'exists:master_data_supplier,id' ],
        ] );

        $sparepart = MasterDataSparepart::findOrFail ( $id );

        $sparepart->update ( $request->only ( [ 'nama', 'part_number', 'merk', 'kategori' ] ) );

        $sparepart->id_kategori_sparepart = $request->input ( 'kategori' );
        $sparepart->save ();

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
