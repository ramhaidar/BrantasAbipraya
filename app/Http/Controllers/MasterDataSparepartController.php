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
        $user      = Auth::user ();
        $proyeks   = [];
        $sparepart = [];

        if ( $user->role === 'Admin' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $sparepart = MasterDataSparepart::orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $sparepart     = MasterDataSparepart::whereIn ( 'id_user', $usersInProyek )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $sparepart = MasterDataSparepart::where ( 'id_user', $user->id )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }

        $suppliers = MasterDataSupplier::all ();
        $kategori  = KategoriSparepart::all ();

        return view ( 'dashboard.masterdata.sparepart.sparepart', [ 
            'proyeks'    => $proyeks,
            'masterData' => $sparepart,
            'suppliers'  => $suppliers,
            'categories' => $kategori,

            'headerPage' => "Master Data Sparepart",
            'page'       => 'Data Sparepart',
        ] );
    }

    public function store ( Request $request )
    {
        $request->validate ( [ 
            'nama'        => [ 'required', 'string', 'max:255' ],
            'part_number' => [ 'required', 'string', 'max:255' ],
            'merk'        => [ 'required', 'string', 'max:255' ],
            'kategori'    => [ 'required', 'exists:kategori_sparepart,id' ], // Validasi id_kategori
            'suppliers'   => [ 'array' ], // Validasi bahwa suppliers adalah array
            'suppliers.*' => [ 'exists:master_data_supplier,id' ], // Pastikan setiap supplier ID valid
        ] );

        // Simpan data utama MasterDataSparepart
        $sparepart              = new MasterDataSparepart;
        $sparepart->nama        = $request->input ( 'nama' );
        $sparepart->part_number = $request->input ( 'part_number' );
        $sparepart->merk        = $request->input ( 'merk' );
        $sparepart->id_kategori = $request->input ( 'kategori' );
        $sparepart->save ();

        // Sinkronisasi suppliers menggunakan relasi many-to-many
        $sparepart->suppliers ()->sync ( $request->input ( 'suppliers', [] ) );

        return redirect ()->route ( 'master_data_sparepart.index' )
            ->with ( 'success', 'Data Master Sparepart berhasil ditambahkan' );
    }

    public function show ( $id )
    {
        $sparepart = MasterDataSparepart::with ( 'suppliers', 'kategori' )->findOrFail ( $id );

        return response ()->json ( [ 
            'data' => [ 
                'id'          => $sparepart->id,
                'nama'        => $sparepart->nama,
                'part_number' => $sparepart->part_number,
                'merk'        => $sparepart->merk,
                'id_kategori' => optional ( $sparepart->kategori )->id, // Include ID kategori
                'kategori'    => $sparepart->kategori ? [ 
                    'nama'      => $sparepart->kategori->nama,
                    'kode'      => $sparepart->kategori->kode,
                    'jenis'     => $sparepart->kategori->jenis,
                    'sub_jenis' => $sparepart->kategori->sub_jenis,
                ] : null,
                'suppliers'   => $sparepart->suppliers->map ( function ($supplier)
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
            'kategori'    => [ 'required', 'exists:kategori_sparepart,id' ], // Validasi id_kategori
            'suppliers'   => [ 'array' ],
            'suppliers.*' => [ 'exists:master_data_supplier,id' ],
        ] );

        // Temukan sparepart berdasarkan ID
        $sparepart = MasterDataSparepart::findOrFail ( $id );

        // Update data sparepart
        $sparepart->update ( $request->only ( [ 'nama', 'part_number', 'merk', 'kategori' ] ) );

        // Update id_kategori
        $sparepart->id_kategori = $request->input ( 'kategori' );
        $sparepart->save ();

        // Sync suppliers, even if empty
        $sparepart->suppliers ()->sync ( $request->input ( 'suppliers', [] ) );

        return redirect ()->route ( 'master_data_sparepart.index' )->with ( 'success', 'Master Data Sparepart berhasil diperbarui' );
    }

    public function destroy ( $id )
    {
        $sparepart = MasterDataSparepart::findOrFail ( $id );
        $sparepart->delete ();

        return redirect ()->route ( 'master_data_sparepart.index' )->with ( 'success', 'Master Data Sparepart berhasil dihapus' );
    }

    public function getData ( Request $request )
    {
        // Create base query with join
        $query = MasterDataSparepart::query ()
            ->leftJoin ( 'kategori_sparepart as kategori', 'master_data_sparepart.id_kategori', '=', 'kategori.id' )
            ->select (
                'master_data_sparepart.*',
                'kategori.kode as kode_kategori',
                'kategori.nama as nama_kategori',
                'kategori.jenis as jenis_kategori',
                'kategori.sub_jenis as sub_jenis_kategori'
            );

        // Apply search filters
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'master_data_sparepart.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.part_number', 'like', "%{$search}%" )
                    ->orWhere ( 'master_data_sparepart.merk', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori.kode', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori.nama', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori.jenis', 'like', "%{$search}%" )
                    ->orWhere ( 'kategori.sub_jenis', 'like', "%{$search}%" );
            } );
        }

        // Apply sorting
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];

            // Map DataTable columns to database columns
            $allowedSortColumns = [ 
                'kode_kategori'      => 'kategori.kode',
                'jenis_kategori'     => 'kategori.jenis',
                'sub_jenis_kategori' => 'kategori.sub_jenis',
                'nama_kategori'      => 'kategori.nama',
                'nama'               => 'master_data_sparepart.nama',
                'part_number'        => 'master_data_sparepart.part_number',
                'merk'               => 'master_data_sparepart.merk',
            ];

            // Apply sorting only if the column is valid
            if ( isset ( $allowedSortColumns[ $columnName ] ) )
            {
                $query->orderBy ( $allowedSortColumns[ $columnName ], $sortDirection );
            }
        }
        else
        {
            $query->orderBy ( 'master_data_sparepart.updated_at', 'desc' );
        }

        // Pagination
        $start           = $request->input ( 'start', 0 );
        $length          = $request->input ( 'length', 10 );
        $totalRecords    = MasterDataSparepart::count ();
        $filteredRecords = $query->count ();

        $spareparts = $query->skip ( $start )->take ( $length )->get ();

        // Transform data for DataTable
        $spareparts->transform ( function ($item)
        {
            $item->detail = $item->suppliers->pluck ( 'nama' )->implode ( ', ' ); // Supplier names
            return $item;
        } );

        // Return JSON response
        return response ()->json ( [ 
            'draw'            => $request->input ( 'draw' ),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $spareparts,
        ] );
    }

    public function getSparepartsByCategory ( $id )
    {
        $spareparts = MasterDataSparepart::where ( 'id_kategori', $id )->get ();

        return response ()->json ( $spareparts );
    }
}
