<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;

class SPBController extends Controller
{
    // Index for SPB
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get ();

        return view ( 'dashboard.spb.spb', [ 
            'proyeks'    => $proyeks,

            'headerPage' => "SPB",
            'page'       => 'Data SPB',
        ] );
    }
}
