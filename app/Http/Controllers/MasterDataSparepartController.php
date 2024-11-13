<?php

namespace App\Http\Controllers;

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
        $user       = Auth::user ();
        $proyeks    = [];
        $masterData = [];

        if ( $user->role === 'Admin' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $masterData = MasterDataSparepart::orderBy ( 'updated_at', 'desc' )
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
            $masterData    = MasterDataSparepart::whereIn ( 'id_user', $usersInProyek )
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

            $masterData = MasterDataSparepart::where ( 'id_user', $user->id )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }

        $suppliers = MasterDataSupplier::all ();

        return view ( 'dashboard.masterdata.sparepart.sparepart', [ 
            'proyeks'    => $proyeks,
            'masterData' => $masterData,
            'suppliers'  => $suppliers,

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
            'suppliers'   => [ 'array' ], // Validasi bahwa suppliers adalah array
            'suppliers.*' => [ 'exists:master_data_suppliers,id' ], // Pastikan setiap supplier ID valid
        ] );

        // Simpan data utama MasterDataSparepart
        $masterData              = new MasterDataSparepart;
        $masterData->nama        = $request->nama;
        $masterData->part_number = $request->part_number;
        $masterData->merk        = $request->merk;
        $masterData->save ();

        // Sinkronisasi suppliers menggunakan relasi many-to-many
        if ( $request->has ( 'suppliers' ) )
        {
            $masterData->suppliers ()->sync ( $request->suppliers );
        }

        return redirect ()->route ( 'master_data_sparepart.index' )
            ->with ( 'success', 'Data Master Sparepart berhasil ditambahkan' );
    }

    public function show ( $id )
    {
        $masterData = MasterDataSparepart::with ( 'suppliers' )->findOrFail ( $id );

        return response ()->json ( [ 
            'data' => $masterData
        ] );
    }

    public function update ( Request $request, $id )
    {
        $request->validate ( [ 
            'nama'        => [ 'required', 'string', 'max:255' ],
            'part_number' => [ 'required', 'string', 'max:255' ],
            'merk'        => [ 'required', 'string', 'max:255' ],
            'suppliers'   => [ 'array' ],
            'suppliers.*' => [ 'exists:master_data_suppliers,id' ],
        ] );

        $masterData = MasterDataSparepart::findOrFail ( $id );
        $masterData->update ( $request->only ( [ 'nama', 'part_number', 'merk' ] ) );

        // Sync suppliers, even if empty
        $masterData->suppliers ()->sync ( $request->input ( 'suppliers', [] ) );

        return redirect ()->route ( 'master_data_sparepart.index' )->with ( 'success', 'Master Data Sparepart berhasil diperbarui' );
    }

    public function destroy ( $id )
    {
        $masterData = MasterDataSparepart::findOrFail ( $id );
        $masterData->delete ();

        return redirect ()->route ( 'master_data_sparepart.index' )->with ( 'success', 'Master Data Sparepart berhasil dihapus' );
    }

    public function getData ( Request $request )
    {
        $query = MasterDataSparepart::with ( 'suppliers' ); // Load suppliers relationship

        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nama', 'like', "%{$search}%" )
                    ->orWhere ( 'part_number', 'like', "%{$search}%" )
                    ->orWhere ( 'merk', 'like', "%{$search}%" );
            } );
        }

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

        $start           = $request->input ( 'start', 0 );
        $length          = $request->input ( 'length', 10 );
        $totalRecords    = MasterDataSparepart::count ();
        $filteredRecords = $query->count ();

        $masterData = $query->skip ( $start )->take ( $length )->get ();

        // Format suppliers into a string for each sparepart
        $masterData->transform ( function ($item)
        {
            $item->detail = $item->suppliers->pluck ( 'nama' )->implode ( ', ' );
            return $item;
        } );

        return response ()->json ( [ 
            'draw'            => $request->input ( 'draw' ),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $masterData,
        ] );
    }

}
