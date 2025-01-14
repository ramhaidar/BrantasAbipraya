<?php

namespace App\Http\Controllers;

use App\Models\AlatProyek;
use App\Models\APB;
use App\Models\RKB;
use App\Models\Proyek;
use App\Models\Alat;
use App\Models\Saldo;
use Illuminate\Http\Request;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class APBController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showApbPage (
            "Hutang Unit Alat",
            "Data APB EX Unit Alat",
            $request->id_proyek
        );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showApbPage (
            "Panjar Unit Alat",
            "Data APB EX Panjar Unit Alat",
            $request->id_proyek
        );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showApbPage (
            "Mutasi Proyek",
            "Data APB EX Mutasi Proyek",
            $request->id_proyek
        );
    }

    public function panjar_proyek ( Request $request )
    {
        return $this->showApbPage (
            "Panjar Proyek",
            "Data APB EX Panjar Proyek",
            $request->id_proyek
        );
    }

    private function showApbPage ( $tipe, $pageTitle, $id_proyek )
    {
        // Ubah nilai $tipe menjadi huruf kecil dan ganti spasi dengan tanda hubung
        $tipe = strtolower ( str_replace ( ' ', '-', $tipe ) );

        $proyek = Proyek::with ( "users" )->find ( $id_proyek );

        // Get alat data for this project
        $alats = AlatProyek::where ( 'id_proyek', $id_proyek )->get ();

        // Modified spareparts query to filter by type and project
        $spareparts = Saldo::where ( 'quantity', '>', 0 )
            ->with ( [ 'masterDataSparepart', 'atb' ] )
            ->whereHas ( 'atb', function ($query) use ($tipe)
            {
                $query->where ( 'tipe', $tipe );
            } )
            ->whereHas ( 'atb', function ($query) use ($id_proyek)
            {
                $query->where ( 'id_proyek', $id_proyek );
            } )
            ->get ()
            ->sortBy ( 'atb.tanggal' );

        // dd ( $spareparts );

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

        $apbs = APB::with ( [ 
            'masterDataSparepart',
            'masterDataSupplier',
            'proyek',
            'alatProyek'
        ] )
            ->where ( 'id_proyek', $id_proyek )
            ->where ( 'tipe', $tipe )
            ->orderBy ( 'tanggal', 'desc' )
            ->orderBy ( 'updated_at', 'asc' )
            ->get ();

        return view ( "dashboard.apb.apb", [ 
            "proyek"     => $proyek,
            "alats"      => $alats,
            "spareparts" => $spareparts,
            "proyeks"    => $proyeks,
            "spbs"       => $filteredSpbs,
            "headerPage" => $proyek->nama,
            "page"       => $pageTitle,
            "tipe"       => $tipe,
            "apbs"       => $apbs,
        ] );
    }

    public function store ( Request $request )
    {
        // Validate request
        $validated = $request->validate ( [ 
            'tanggal'                  => 'required|date',
            'id_proyek'                => 'required|exists:proyek,id',
            'id_alat'                  => 'required|exists:alat_proyek,id',
            'id_master_data_sparepart' => 'required|exists:master_data_sparepart,id',
            'quantity'                 => 'required|integer|min:1',
            'tipe'                     => 'required|string',
            // Removed root_cause validation
            'mekanik'                  => 'required|string|max:255'
        ] );

        try
        {
            // Start transaction
            DB::beginTransaction ();

            // Find the saldo with available quantity
            $saldo = Saldo::where ( 'id_master_data_sparepart', $request->id_master_data_sparepart )
                ->where ( 'quantity', '>', 0 )
                ->orderBy ( 'id', 'asc' )
                ->firstOrFail ();

            // Check if requested quantity is available
            if ( $saldo->quantity < $request->quantity )
            {
                throw new \Exception( 'Stok sparepart tidak mencukupi.' );
            }

            // Create APB record
            $apb = APB::create ( [ 
                'tanggal'                  => $request->tanggal,
                'tipe'                     => $request->tipe,
                // Removed root_cause
                'mekanik'                  => $request->mekanik,
                'quantity'                 => $request->quantity,
                'id_saldo'                 => $saldo->id,
                'id_proyek'                => $request->id_proyek,
                'id_master_data_sparepart' => $request->id_master_data_sparepart,
                'id_master_data_supplier'  => $saldo->id_master_data_supplier,
                'id_alat_proyek'           => $request->id_alat
            ] );

            // Decrement quantity
            $saldo->decrementQuantity ( $request->quantity );

            DB::commit ();

            return redirect ()->back ()
                ->with ( 'success', 'Data APB berhasil ditambahkan.' );

        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()
                ->with ( 'error', 'Gagal menambahkan data APB: ' . $e->getMessage () );
        }
    }

    public function destroy ( $id )
    {
        try
        {
            // Start transaction
            DB::beginTransaction ();

            // Find the APB record
            $apb = APB::findOrFail ( $id );

            // Increment the quantity back to the saldo
            $apb->saldo->incrementQuantity ( $apb->quantity );

            // Delete the APB record
            $apb->delete ();

            DB::commit ();

            return redirect ()->back ()->with ( 'success', 'Data APB berhasil dihapus.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal menghapus data APB: ' . $e->getMessage () );
        }
    }
}
