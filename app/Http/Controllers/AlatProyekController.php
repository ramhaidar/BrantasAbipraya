<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AlatProyekController extends Controller
{
    public function index ( Request $request )
    {
        $user    = Auth::user ();
        $proyeks = [];
        $alat    = [];

        // Ambil proyek berdasarkan ID
        $proyek = Proyek::with ( "users" )->find ( $request->id_proyek );

        if ( ! $proyek )
        {
            abort ( 404, "Proyek tidak ditemukan" );
        }

        // Role-based logic
        if ( $user->role === 'Admin' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $alat = MasterDataAlat::orderBy ( 'updated_at', 'desc' )
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
            $alat          = MasterDataAlat::whereIn ( 'id_user', $usersInProyek )
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

            $alat = MasterDataAlat::where ( 'id_user', $user->id )
                ->orderBy ( 'updated_at', 'desc' )
                ->paginate ( $request->input ( 'length', 10 ) );
        }

        // Kirim data ke view
        return view ( 'dashboard.alat.alat', [ 
            'proyeks'    => $proyeks,
            'proyek'     => $proyek,
            'alat'       => $alat,
            'headerPage' => "Data Alat Proyek",
            'page'       => 'Data Alat',
        ] );
    }
}
