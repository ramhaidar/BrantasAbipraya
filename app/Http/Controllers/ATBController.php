<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ATB;
use App\Models\RKB;
use App\Models\SPB;
use App\Models\User;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\Komponen;
use App\Models\DetailSPB;
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

        $rkbs = RKB::with ( "spbs" )->where ( 'id_proyek', $id_proyek )->get ();

        $spbs = collect ();

        foreach ( $rkbs as $rkb )
        {
            $spbs = $spbs->merge ( $rkb->spbs );
        }

        $spbs = $spbs->unique ( 'id' );

        // Group SPBs by their base number (without the dash and following digits)
        $groupedSpbs = $spbs->groupBy ( function ($spb)
        {
            return preg_replace ( '/-\d+$/', '', $spb->nomor_spb );
        } );

        // Get the SPB with the largest number for each group
        $filteredSpbs = $groupedSpbs->map ( function ($group)
        {
            return $group->sortByDesc ( function ($spb)
            {
                return intval ( preg_replace ( '/^.*-/', '', $spb->nomor_spb ) );
            } )->first ();
        } );

        // Merge the filtered SPBs back into the original collection
        $spbs = $spbs->merge ( $filteredSpbs )->unique ( 'id' );

        dd ( $groupedSpbs );

        return view ( "dashboard.atb.atb", [ 
            "proyek"     => $proyek,
            "proyeks"    => $proyeks,
            "spbs"       => $spbs,
            "headerPage" => $proyek->nama_proyek,
            "page"       => $pageTitle,
        ] );
    }

    public function getlinkSpbDetailSpbs ( $id )
    {
        $SPB = SPB::with ( "linkSpbDetailSpb.detailSpb.MasterDataSparepart" )->find ( $id );

        $DetailSPB = [];

        foreach ( $SPB->linkSpbDetailSpb as $item )
        {
            $DetailSPB[] = $item->detailSpb;
        }

        return response ()->json ( $DetailSPB );
    }
}