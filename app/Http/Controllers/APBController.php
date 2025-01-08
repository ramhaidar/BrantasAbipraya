<?php

namespace App\Http\Controllers;

use App\Models\ATB;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APBController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showApbPage (
            "Hutang Unit Alat",
            "Data ATB Hutang Unit Alat",
            $request->id_proyek
        );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showApbPage (
            "Panjar Unit Alat",
            "Data ATB Panjar Unit Alat",
            $request->id_proyek
        );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showApbPage (
            "Mutasi Proyek",
            "Data ATB Mutasi Proyek",
            $request->id_proyek
        );
    }

    public function panjar_proyek ( Request $request )
    {
        return $this->showApbPage (
            "Panjar Proyek",
            "Data ATB Panjar Proyek",
            $request->id_proyek
        );
    }

    private function showApbPage ( $tipe, $pageTitle, $id_proyek )
    {
        // Ubah nilai $tipe menjadi huruf kecil dan ganti spasi dengan tanda hubung
        $tipe = strtolower ( str_replace ( ' ', '-', $tipe ) );

        $proyek = Proyek::with ( "users" )->find ( $id_proyek );

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "asc" )
            ->orderBy ( "id", "asc" )
            ->get ();

        $rkbs = RKB::with ( "spbs.linkSpbDetailSpb.detailSpb" )->where ( 'id_proyek', $id_proyek )->get ();
        $spbs = collect ();

        foreach ( $rkbs as $rkb )
        {
            $spbs = $spbs->merge ( $rkb->spbs );
        }

        $filteredSpbs = collect ();

        foreach ( $spbs as $index => $spb )
        {
            $allZero = true;
            foreach ( $spb->linkSpbDetailSpb as $link )
            {
                if ( $link->detailSpb->quantity_belum_diterima > 0 )
                {
                    $allZero = false;
                    break;
                }
            }

            if ( ! $allZero )
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
        }

        // Ambil data ATB dari database dengan relasi
        $atbs = ATB::with ( [ 'spb', 'masterDataSparepart' ] )
            ->where ( 'id_proyek', $id_proyek )
            ->where ( 'tipe', $tipe )
            ->get ();

        return view ( "dashboard.apb.apb", [ 
            "proyek"     => $proyek,
            "proyeks"    => $proyeks,
            "spbs"       => $filteredSpbs,
            "headerPage" => $proyek->nama,
            "page"       => $pageTitle,
            "atbs"       => $atbs, // Kirim data ATB ke view
        ] );
    }
}
