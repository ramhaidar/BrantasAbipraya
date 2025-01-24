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
                $q->where ( 'nama', 'like', "%{$search}%" )
                    ->orWhere ( 'part_number', 'like', "%{$search}%" )
                    ->orWhere ( 'merk', 'like', "%{$search}%" )
                    ->orWhereHas ( 'kategoriSparepart', function ($query) use ($search)
                    {
                        $query->where ( 'nama', 'like', "%{$search}%" );
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

        $spareparts = $query->paginate ( $perPage )
            ->withQueryString ();

        $TableData = $query->paginate ( $perPage )
            ->withQueryString ();

        $suppliers = MasterDataSupplier::all ();
        $kategori  = KategoriSparepart::all ();
        $proyeks   = [];

        if ( $user->role !== 'Pegawai' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();
        }

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

    public function getData ( Request $request )
    {
        // Start query with join
        $query = MasterDataSparepart::join ( 'kategori_sparepart', 'master_data_sparepart.id_kategori_sparepart', '=', 'kategori_sparepart.id' )
            ->select ( [ 
                'master_data_sparepart.*',
                'kategori_sparepart.kode as kode_kategori',
                'kategori_sparepart.nama as nama_kategori',
                'kategori_sparepart.jenis as jenis_kategori',
                'kategori_sparepart.sub_jenis as sub_jenis_kategori'
            ] );

        // Apply search filters
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'master_data_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.part_number', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.merk', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.kode', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.jenis', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori_sparepart.sub_jenis', 'like', "%{$search}%" );
            } );
        }

        // Apply sorting
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];

            // Map kolom DataTable ke kolom database
            $columnMap = [ 
                'nama'               => 'master_data_sparepart.nama',
                'part_number'        => 'master_data_sparepart.part_number',
                'merk'               => 'master_data_sparepart.merk',
                'kode_kategori'      => 'kategori_sparepart.kode',
                'jenis_kategori'     => 'kategori_sparepart.jenis',
                'sub_jenis_kategori' => 'kategori_sparepart.sub_jenis',
                'nama_kategori'      => 'kategori_sparepart.nama'
            ];

            if ( isset ( $columnMap[ $columnName ] ) )
            {
                $query->orderBy ( $columnMap[ $columnName ], $sortDirection );
            }
        }
        else
        {
            $query->orderBy ( 'master_data_sparepart.updated_at', 'desc' );
        }

        // Pagination
        $totalRecords    = MasterDataSparepart::count ();
        $start           = $request->input ( 'start', 0 );
        $length          = $request->input ( 'length', 10 );
        $filteredRecords = $query->count ();

        $spareparts = $query->skip ( $start )->take ( $length )->get ();

        // Load relasi suppliers untuk setiap sparepart
        $spareparts->load ( 'masterDataSuppliers' );

        // Transform data untuk DataTable
        $data = $spareparts->map ( function ($item)
        {
            return [ 
                'id'                 => $item->id,
                'nama'               => $item->nama,
                'part_number'        => $item->part_number,
                'merk'               => $item->merk,
                'kode_kategori'      => $item->kode_kategori ?? '-',
                'jenis_kategori'     => $item->jenis_kategori ?? '-',
                'sub_jenis_kategori' => $item->sub_jenis_kategori ?? '-',
                'nama_kategori'      => $item->nama_kategori ?? '-',
                'supplier'           => $item->masterDataSuppliers->pluck ( 'nama' )->implode ( ', ' )
            ];
        } );

        return response ()->json ( [ 
            'draw'            => $request->input ( 'draw' ),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data
        ] );
    }

}
