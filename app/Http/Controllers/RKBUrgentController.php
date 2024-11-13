<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;

class RKBUrgentController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get ();

        return view ( 'dashboard.rkb.urgent.urgent', [ 
            'proyeks'    => $proyeks,

            'headerPage' => "RKB Urgent",
            'page'       => 'Data RKB Urgent',
        ] );
    }
}
