<?php
namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterDataAlatController extends Controller
{
    public function index ( Request $request )
    {
        $user = Auth::user ();

        // Inisialisasi variabel $proyeks dan $alat
        $proyeks = [];
        $alat    = [];

        if ( $user->role === 'Admin' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $alat = Alat::with ( 'proyek', 'user' )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) ); // Default 10 rows per page
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $alat          = Alat::whereIn ( 'id_user', $usersInProyek )
                ->with ( 'proyek', 'user' )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $alat = Alat::where ( 'id_user', $user->id )
                ->with ( 'proyek', 'user' )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }

        // Render tampilan dengan data paginasi
        return view ( 'dashboard.masterdata.alat.alat', [ 
            'proyeks'    => $proyeks,
            'proyek'     => $proyeks,
            'alat'       => $alat,
            'headerPage' => "Master Data Alat",
            'page'       => 'Data Alat',
        ] );
    }

    public function store ( Request $request )
    {
        $request->validate ( [ 
            'nama_proyek' => [ 'string', 'nullable' ],
            'jenis_alat'  => [ 'required', 'string' ],
            'merek_alat'  => [ 'required', 'string' ],
            'tipe_alat'   => [ 'required', 'string' ],
            'kode_alat'   => [ 'required', 'string' ],
        ] );

        $alat              = new Alat;
        $alat->nama_proyek = $request->nama_proyek;
        $alat->jenis_alat  = $request->jenis_alat;
        $alat->merek_alat  = $request->merek_alat;
        $alat->tipe_alat   = $request->tipe_alat;
        $alat->kode_alat   = $request->kode_alat;
        $alat->id_user     = Auth::id (); // Menyimpan ID user yang menambahkan alat
        $alat->save ();

        return back ()->with ( 'success', 'Data Alat Berhasil Ditambahkan' );
    }

    public function show ( $id )
    {
        $alat = Alat::find ( $id );
        if ( $alat )
        {
            return response ()->json ( [ 'message' => 'Data barang berhasil ditemukan', 'data' => $alat ] );
        }
        else
        {
            return response ()->json ( [ 'message' => 'Barang not found' ], 404 );
        }
    }

    public function update ( Request $request, $id )
    {
        $alat = Alat::find ( $id );
        if ( $alat )
        {
            $request->validate ( [ 
                'nama_proyek' => [ 'string', 'nullable' ],
                'jenis_alat'  => [ 'required', 'string' ],
                'merek_alat'  => [ 'required', 'string' ],
                'tipe_alat'   => [ 'required', 'string' ],
                'kode_alat'   => [ 'required', 'string' ],
            ] );

            $alat->nama_proyek = $request->nama_proyek;
            $alat->jenis_alat  = $request->jenis_alat;
            $alat->merek_alat  = $request->merek_alat;
            $alat->tipe_alat   = $request->tipe_alat;
            $alat->kode_alat   = $request->kode_alat;
            $alat->save ();

            return back ()->with ( 'success', 'Mengubah Data Barang' );
        }
        else
        {
            return back ()->with ( 'error', 'Barang tidak ditemukan!' );
        }
    }

    public function destroy ( $id )
    {
        $alat = Alat::find ( $id );
        if ( ! $alat )
        {
            return response ()->json ( [ 'message' => 'Alat not found' ], 404 );
        }
        $alat->delete ();
        return response ()->json ( [ 'success' => 'Menghapus Data Barang' ] );
    }

    public function getData ( Request $request )
    {
        $query = Alat::query ()->with ( 'proyek', 'user' );

        // Filter berdasarkan search term
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nama_proyek', 'like', "%{$search}%" )
                    ->orWhere ( 'jenis_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'kode_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'merek_alat', 'like', "%{$search}%" )
                    ->orWhere ( 'tipe_alat', 'like', "%{$search}%" );
            } );
        }

        // Sorting
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];
            $query->orderBy ( $columnName, $sortDirection );
        }
        else
        {
            $query->orderBy ( 'updated_at', 'desc' ); // Default order
        }

        // Handle pagination
        $start           = $request->input ( 'start', 0 );
        $length          = $request->input ( 'length', 10 );
        $totalRecords    = Alat::count (); // Total records without filtering
        $filteredRecords = $query->count (); // Total records after filtering

        // Apply pagination
        $alat = $query->skip ( $start )->take ( $length )->get ();

        return response ()->json ( [ 
            'draw'            => $request->input ( 'draw' ),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $alat,
        ] );
    }





}