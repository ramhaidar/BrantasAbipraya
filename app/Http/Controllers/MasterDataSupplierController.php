<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Proyek;
use Illuminate\Http\Request;

class MasterDataSupplierController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();

        $alat = Alat::with ( 'proyek', 'user' )
            ->orderBy ( 'updated_at', 'desc' )
            ->get ();
        return view ( 'dashboard.masterdata.supplier.supplier', [ 
            'proyek'     => $proyeks,
            'alat'       => $alat,
            'headerPage' => "Master Data Supplier",
            'page'       => 'Data Supplier',
            'proyeks'    => $proyeks,
        ] );
    }
}
