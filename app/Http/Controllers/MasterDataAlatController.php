<?php
namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterDataAlatController extends Controller
{
    public function index ()
    {
        $user = Auth::user ();

        // Inisialisasi variabel $proyeks
        $proyeks = [];

        if ( $user->role === 'Admin' )
        {
            // Jika user adalah Admin, ambil semua proyek dengan relasi users
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            // Mengambil semua alat dengan relasi proyek dan user untuk Admin
            $alat = Alat::with ( 'proyek', 'user' )
                // ->orderByDesc ( 'updated_at' )
                ->orderBy ( 'updated_at', 'desc' )
                ->get ();
        }
        elseif ( $user->role === 'Boss' )
        {
            // Jika user adalah Boss, ambil proyek yang terkait dengan boss tersebut
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            // Ambil user dari proyek-proyek tersebut
            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();

            // Mengambil semua alat yang terkait dengan user dari proyek yang bisa diakses oleh boss
            $alat = Alat::whereIn ( 'id_user', $usersInProyek )
                ->with ( 'proyek', 'user' ) // Asumsikan alat terkait proyek dan user
                ->orderBy ( 'updated_at', 'desc' )
                ->get ();
        }
        elseif ( $user->role === 'Pegawai' )
        {
            // Jika user bukan Admin, ambil proyek yang terkait dengan user
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            // Mengambil alat yang diassign kepada Pegawai berdasarkan id_user saja
            $alat = Alat::where ( 'id_user', $user->id )
                ->with ( 'proyek', 'user' )
                // ->orderByDesc ( 'updated_at' )
                ->orderBy ( 'updated_at', 'desc' )
                ->get ();

        }

        // Render tampilan dengan data yang difilter
        return view ( 'dashboard.masterdata.alat.alat', [ 
            'proyek'     => $proyeks,
            'alat'       => $alat,
            'headerPage' => "Master Data Alat",
            'page'       => 'Data Alat',
            'proyeks'    => $proyeks,
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
}