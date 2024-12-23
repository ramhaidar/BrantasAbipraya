<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ATB;
use App\Models\User;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\Komponen;
use App\Exports\ATBExport;
use App\Imports\ATBImport;
use App\Models\FirstGroup;
use App\Models\MasterData;
use App\Models\SecondGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ATBController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showAtbPage (
            "Hutang Unit Alat",
            "Data ATB Hutang Unit Alat",
            $request->id_proyek
        );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showAtbPage (
            "Panjar Unit Alat",
            "Data ATB Panjar Unit Alat",
            $request->id_proyek
        );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showAtbPage (
            "Mutasi Proyek",
            "Data ATB Mutasi Proyek",
            $request->id_proyek
        );
    }

    public function panjar_proyek ( Request $request )
    {
        return $this->showAtbPage (
            "Panjar Proyek",
            "Data ATB Panjar Proyek",
            $request->id_proyek
        );
    }

    private function showAtbPage ( $tipe, $pageTitle, $id_proyek )
    {
        $proyek = Proyek::with ( "users" )->find ( $id_proyek );

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "asc" )
            ->orderBy ( "id", "asc" )
            ->get ();

        return view ( "dashboard.atb.atb", [ 
            // "page"              => $page,
            "proyek"     => $proyek,
            "proyeks"    => $proyeks,

            "headerPage" => $proyek->nama_proyek,
            "page"       => $pageTitle,
        ] );
    }
}