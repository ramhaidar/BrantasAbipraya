<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LinkAlatDetailRKB;
use App\Models\MasterDataSparepart;
use App\Models\MasterDataSupplier;
use App\Models\Proyek;
use App\Models\RKB;

class DetailSPBController extends Controller
{
    public function index($id)
    {
        $proyeks = Proyek::with("users")->orderByDesc("updated_at")->get();
        $rkb = RKB::with([
            "linkAlatDetailRkbs.masterDataAlat",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart" => function ($query) {
                $query->orderBy('nama', 'asc');
            },
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.masterDataSparepart" => function ($query) {
                $query->orderBy('nama', 'asc');
            },
            "linkAlatDetailRkbs.timelineRkbUrgents",
            "linkAlatDetailRkbs.lampiranRkbUrgent",
        ])->find($id);

        $supplier = MasterDataSupplier::all();

        return view('dashboard.spb.detail.detail', [
            'proyeks' => $proyeks,
            'rkb' => $rkb,
            'supplier' => $supplier,

            'headerPage' => "SPB",
            'page' => 'Detail SPB',
        ]);
    }

    public function getSparepart($idLinkAlatDetail)
    {
        try {
            $linkAlatDetailRkb = LinkAlatDetailRKB::with(
                'linkRkbDetails.detailRkbGeneral.masterDataSparepart',
                'linkRkbDetails.detailRkbUrgent.masterDataSparepart',
            )->findOrFail($idLinkAlatDetail);

            $linkRkbDetails = $linkAlatDetailRkb->linkRkbDetails;

            $spareparts = [];
            foreach ($linkRkbDetails as $detail) {
                if ($detail->detailRkbGeneral && $detail->detailRkbGeneral->masterDataSparepart) {
                    $spareparts[] = $detail->detailRkbGeneral->masterDataSparepart;
                }
                if ($detail->detailRkbUrgent && $detail->detailRkbUrgent->masterDataSparepart) {
                    $spareparts[] = $detail->detailRkbUrgent->masterDataSparepart;
                }
            }

            return response()->json($spareparts);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getSupplier($idMasterDataSparepart)
    {
        try {
            $sparepartName = MasterDataSparepart::where('id', $idMasterDataSparepart)->value('nama');

            $suppliers = [];
            foreach (MasterDataSparepart::with('suppliers')->where('nama', $sparepartName)->get() as $detail) {
                foreach ($detail->suppliers as $supplier) {
                    $suppliers[] = $supplier;
                }
            }

            return response()->json($suppliers);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getMerk($idMasterDataSparepart)
    {
        try {
            $sparepartName = MasterDataSparepart::where('id', $idMasterDataSparepart)->value('nama');

            return response()->json($sparepartName);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
