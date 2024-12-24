<?php
namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\SPB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    protected function console ( $message )
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln ( $message );
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

        $filteredSpbs = collect ();

        foreach ( $spbs as $index => $spb )
        {
            if ( $spb->is_addendum == false && ! isset ( $spb->id_spb_original ) )
            {
                $filteredSpbs->push ( $spb );
            }

            if ( $spb->is_addendum == true && isset ( $spb->id_spb_original ) )
            {
                $filteredSpbs->push ( $spb );
            }
        }

        return view ( "dashboard.atb.atb", [ 
            "proyek"     => $proyek,
            "proyeks"    => $proyeks,
            "spbs"       => $filteredSpbs,
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

        $html = view('dashboard.atb.partials.spb-details-table', ['spbDetails' => $DetailSPB])->render();

        return response()->json(['html' => $html]);
    }
}