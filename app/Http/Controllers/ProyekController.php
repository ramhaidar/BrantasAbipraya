<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\UserProyek;
use Illuminate\Http\Request;

class ProyekController extends Controller
{
    public function index ()
    {
        $proyek = Proyek::get ()->all ();

        return view ( "dashboard.proyek.proyek", [ 
            "page"    => "Data Proyek",
            "proyek"  => $proyek,
            "proyeks" => Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get (),
        ] );
    }

    public function store ( Request $request )
    {
        $credentials = $request->validate ( [ 
            "nama_proyek" => "required",
        ] );
        Proyek::create ( $credentials );
        return back ()->with ( "success", "Berhasil menambahkan data proyek." );
    }

    public function showByID ( Proyek $id )
    {
        $id->load ( 'users' );

        return response ()->json ( $id );
    }
    public function update ( Request $request, Proyek $id )
    {
        $credentials = $request->validate ( [ 
            "nama_proyek" => "required",
        ] );
        $id->update ( $credentials );
        $id->save ();

        return back ()->with ( "success", "Berhasil mengubah data proyek" );
    }
    public function destroy ( Proyek $id )
    {
        $id->delete ();
        $msg = "Berhasil menghapus data proyek";

        return back ()->with ( "success", $msg );
    }
}
