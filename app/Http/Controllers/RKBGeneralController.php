<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;

class RKBGeneralController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get ();

        return view ( 'dashboard.rkb.general.general', [ 
            'proyeks'    => $proyeks,

            'headerPage' => "RKB General",
            'page'       => 'Data RKB General',
        ] );
    }
}
