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
        $perPage = $this->getPerPage ( $request );
        $query   = $this->buildQuery ( $request );

        $uniqueValues = $this->getUniqueValues ( clone $query );
        $suppliers    = $this->getTableData ( $query, $perPage );
        $spareparts   = $this->getSpareparts ();
        $proyeks      = $this->getProyeks ();
        $TableData    = $this->getPaginatedData ( $perPage );

        return view ( 'dashboard.masterdata.supplier.supplier', [ 
            'headerPage'   => "Master Data Supplier",
            'page'         => 'Data Supplier',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'suppliers'    => $suppliers,
            'spareparts'   => $spareparts,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    private function getPerPage ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        return in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;
    }

    private function buildQuery ( Request $request )
    {
        $query = MasterDataSupplier::query ()
            ->with ( [ 'masterDataSpareparts' ] )
            ->orderBy ( $request->get ( 'sort', 'updated_at' ), $request->get ( 'direction', 'desc' ) );

        $this->applySearchFilter ( $request, $query );
        $this->applyUserRoleFilter ( $query );
        $this->applyColumnFilters ( $request, $query );

        return $query;
    }

    private function applySearchFilter ( Request $request, $query )
    {
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
    }

    private function applyUserRoleFilter ( $query )
    {
        $user = Auth::user ();
        if ( $user->role === 'Pegawai' )
        {
            $query->where ( 'id_user', $user->id );
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks       = $user->proyek ()->with ( "users" )->get ();
            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $query->whereIn ( 'id_user', $usersInProyek );
        }
    }

    private function applyColumnFilters ( Request $request, $query )
    {
        $this->handleNamaFilter ( $request, $query );
        $this->handleAlamatFilter ( $request, $query );
        $this->handleContactPersonFilter ( $request, $query );
    }

    private function getUniqueValues ( $query )
    {
        return [ 
            'nama'           => $query->get ()->pluck ( 'nama' )->unique ()->values (),
            'alamat'         => $query->get ()->pluck ( 'alamat' )->unique ()->values (),
            'contact_person' => $query->get ()->pluck ( 'contact_person' )->unique ()->values (),
        ];
    }

    private function getTableData ( $query, $perPage )
    {
        return $query->paginate ( $perPage )->withQueryString ();
    }

    private function getSpareparts ()
    {
        return MasterDataSparepart::all ();
    }

    private function getProyeks ()
    {
        $user         = Auth::user ();
        $proyeksQuery = Proyek::with ( "users" );

        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        return $proyeksQuery->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();
    }

    private function getPaginatedData ( $perPage )
    {
        return MasterDataSupplier::query ()
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();
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

    private function handleAlamatFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_alamat' ) )
        {
            $alamat = explode ( ',', $request->selected_alamat );
            if ( in_array ( 'null', $alamat ) )
            {
                $nonNullValues = array_filter ( $alamat, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'alamat' )
                        ->orWhere ( 'alamat', '-' )
                        ->orWhereIn ( 'alamat', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'alamat', $alamat );
            }
        }
    }

    private function handleContactPersonFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_contact_person' ) )
        {
            $contactPerson = explode ( ',', $request->selected_contact_person );
            if ( in_array ( 'null', $contactPerson ) )
            {
                $nonNullValues = array_filter ( $contactPerson, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'contact_person' )
                        ->orWhere ( 'contact_person', '-' )
                        ->orWhereIn ( 'contact_person', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'contact_person', $contactPerson );
            }
        }
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
