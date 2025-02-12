<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\SPB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DetailSPBProyekController extends Controller
{
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue )
        {
            return [];
        }

        try
        {
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            return [];
        }
    }

    public function index ( Request $request, $id )
    {
        $rkb = RKB::findOrFail ( $id );

        // Get selected filter values
        $selectedJenisAlat        = $this->getSelectedValues ( request ( 'selected_jenis_alat' ) );
        $selectedKodeAlat         = $this->getSelectedValues ( request ( 'selected_kode_alat' ) );
        $selectedKategori         = $this->getSelectedValues ( request ( 'selected_kategori' ) );
        $selectedSparepart        = $this->getSelectedValues ( request ( 'selected_sparepart' ) );
        $selectedMerk             = $this->getSelectedValues ( request ( 'selected_merk' ) );
        $selectedSupplier         = $this->getSelectedValues ( request ( 'selected_supplier' ) );
        $selectedQuantityPO       = $this->getSelectedValues ( request ( 'selected_quantity_po' ) ); // Add this line
        $selectedQuantityDiterima = $this->getSelectedValues ( request ( 'selected_quantity_diterima' ) );
        $selectedSatuan           = $this->getSelectedValues ( request ( 'selected_satuan' ) );
        $selectedHarga            = $this->getSelectedValues ( request ( 'selected_harga' ) );
        $selectedJumlahHarga      = $this->getSelectedValues ( request ( 'selected_jumlah_harga' ) );

        $spbs = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataAlat',
            'linkSpbDetailSpb.detailSpb.masterDataSparepart.kategoriSparepart',
            'linkSpbDetailSpb.detailSpb.atbs',
            'masterDataSupplier',
            'originalSpb.addendums',
        ] )
            ->where ( 'is_addendum', false )
            ->whereIn ( 'id', $rkb->spbs ()->pluck ( 'id_spb' ) )
            ->get ();

        // Apply filters if any are selected
        if (
            ! empty ( $selectedJenisAlat ) || ! empty ( $selectedKodeAlat ) || ! empty ( $selectedKategori ) ||
            ! empty ( $selectedSparepart ) || ! empty ( $selectedMerk ) || ! empty ( $selectedSupplier ) ||
            ! empty ( $selectedQuantityPO ) || ! empty ( $selectedQuantityDiterima ) || ! empty ( $selectedSatuan ) ||
            ! empty ( $selectedHarga ) || ! empty ( $selectedJumlahHarga )
        )
        {

            $spbs = $spbs->filter ( function ($item) use ($selectedJenisAlat, $selectedKodeAlat, $selectedKategori, $selectedSparepart, $selectedMerk, $selectedSupplier, $selectedQuantityPO, $selectedQuantityDiterima, $selectedSatuan, $selectedHarga, $selectedJumlahHarga)
            {
                $spb = $item; // Store item in spb variable for use in closure
                return $spb->linkSpbDetailSpb->contains ( function ($detail) use ($spb, $selectedJenisAlat, $selectedKodeAlat, $selectedKategori, $selectedSparepart, $selectedMerk, $selectedSupplier, $selectedQuantityPO, $selectedQuantityDiterima, $selectedSatuan, $selectedHarga, $selectedJumlahHarga)
                {
                    return ( ! $selectedJenisAlat || in_array ( $detail->detailSpb->masterDataAlat->jenis_alat, $selectedJenisAlat ) ) &&
                        ( ! $selectedKodeAlat || in_array ( $detail->detailSpb->masterDataAlat->kode_alat, $selectedKodeAlat ) ) &&
                        ( ! $selectedKategori || in_array ( $detail->detailSpb->masterDataSparepart->kategoriSparepart->nama, $selectedKategori ) ) &&
                        ( ! $selectedSparepart || in_array ( $detail->detailSpb->masterDataSparepart->nama, $selectedSparepart ) ) &&
                        ( ! $selectedMerk || in_array ( $detail->detailSpb->masterDataSparepart->merk, $selectedMerk ) ) &&
                        ( ! $selectedSupplier || in_array ( $spb->masterDataSupplier->nama, $selectedSupplier ) ) &&
                        ( ! $selectedQuantityPO || in_array ( (string) $detail->detailSpb->quantity_po, $selectedQuantityPO ) ) &&
                        ( ! $selectedQuantityDiterima || in_array ( (string) $detail->detailSpb->atbs->sum ( 'quantity' ), $selectedQuantityDiterima ) ) &&
                        ( ! $selectedSatuan || in_array ( $detail->detailSpb->satuan, $selectedSatuan ) ) &&
                        ( ! $selectedHarga || in_array ( (string) $detail->detailSpb->harga, $selectedHarga ) ) &&
                        ( ! $selectedJumlahHarga || in_array ( (string) ( $detail->detailSpb->harga * $detail->detailSpb->quantity_po ), $selectedJumlahHarga ) );
                } );
            } );
        }

        // Collect unique values for filters
        $uniqueValues = $spbs->flatMap ( function ($spb)
        {
            return $spb->linkSpbDetailSpb->map ( function ($detail) use ($spb)
            {
                return [ 
                    'jenis_alat' => $detail->detailSpb->masterDataAlat->jenis_alat,
                    'kode_alat'  => $detail->detailSpb->masterDataAlat->kode_alat,
                    'kategori'   => $detail->detailSpb->masterDataSparepart->kategoriSparepart->nama,
                    'sparepart'  => $detail->detailSpb->masterDataSparepart->nama,
                    'merk'       => $detail->detailSpb->masterDataSparepart->merk,
                    'supplier'   => $spb->masterDataSupplier->nama
                ];
            } );
        } );

        // Extract unique values
        $uniqueJenisAlat        = $uniqueValues->pluck ( 'jenis_alat' )->unique ()->sort ()->values ();
        $uniqueKodeAlat         = $uniqueValues->pluck ( 'kode_alat' )->unique ()->sort ()->values ();
        $uniqueKategori         = $uniqueValues->pluck ( 'kategori' )->unique ()->sort ()->values ();
        $uniqueSparepart        = $uniqueValues->pluck ( 'sparepart' )->unique ()->sort ()->values ();
        $uniqueMerk             = $uniqueValues->pluck ( 'merk' )->unique ()->sort ()->values ();
        $uniqueSupplier         = $uniqueValues->pluck ( 'supplier' )->unique ()->sort ()->values ();
        $uniqueQuantityPO       = $spbs->flatMap ( function ($spb)
        {
            return $spb->linkSpbDetailSpb->map ( function ($detail)
            {
                return (string) $detail->detailSpb->quantity_po;
            } );
        } )->unique ()->sort ()->values ();
        $uniqueQuantityDiterima = $spbs->flatMap ( function ($spb)
        {
            return $spb->linkSpbDetailSpb->map ( function ($detail)
            {
                return (string) $detail->detailSpb->atbs->sum ( 'quantity' );
            } );
        } )->unique ()->sort ()->values ();

        $uniqueSatuan = $spbs->flatMap ( function ($spb)
        {
            return $spb->linkSpbDetailSpb->map ( function ($detail)
            {
                return $detail->detailSpb->satuan;
            } );
        } )->unique ()->sort ()->values ();

        $uniqueHarga = $spbs->flatMap ( function ($spb)
        {
            return $spb->linkSpbDetailSpb->map ( function ($detail)
            {
                return (string) $detail->detailSpb->harga;
            } );
        } )->unique ()->sort ()->values ();

        $uniqueJumlahHarga = $spbs->flatMap ( function ($spb)
        {
            return $spb->linkSpbDetailSpb->map ( function ($detail)
            {
                return (string) ( $detail->detailSpb->harga * $detail->detailSpb->quantity_po );
            } );
        } )->unique ()->sort ()->values ();

        // Create paginator
        $TableData = new \Illuminate\Pagination\LengthAwarePaginator(
            $spbs->forPage ( $request->get ( 'page', 1 ), 10 ),
            $spbs->count (),
            10,
            $request->get ( 'page', 1 ),
            [ 'path' => $request->url (), 'query' => $request->query () ]
        );

        // Filter projects based on user role
        $user         = Auth::user ();
        $proyeksQuery = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        $proyeks = $proyeksQuery
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $TableData->sortByDesc ( 'updated_at' )
            ->sortByDesc ( 'id' );

        return view ( 'dashboard.spb.proyek.detail.detail', [ 
            'headerPage'               => "SPB Proyek",
            'page'                     => "Detail SPB Proyek [{$rkb->proyek->nama} | {$rkb->nomor}]",
            'proyeks'                  => $proyeks,
            'TableData'                => $TableData,
            'rkb'                      => $rkb,
            'supplier'                 => MasterDataSupplier::all (),
            // Add filter data
            'selectedJenisAlat'        => $selectedJenisAlat,
            'selectedKodeAlat'         => $selectedKodeAlat,
            'selectedKategori'         => $selectedKategori,
            'selectedSparepart'        => $selectedSparepart,
            'selectedMerk'             => $selectedMerk,
            'selectedSupplier'         => $selectedSupplier,
            'uniqueJenisAlat'          => $uniqueJenisAlat,
            'uniqueKodeAlat'           => $uniqueKodeAlat,
            'uniqueKategori'           => $uniqueKategori,
            'uniqueSparepart'          => $uniqueSparepart,
            'uniqueMerk'               => $uniqueMerk,
            'uniqueSupplier'           => $uniqueSupplier,
            'selectedQuantityPO'       => $selectedQuantityPO,
            'uniqueQuantityPO'         => $uniqueQuantityPO,
            'selectedQuantityDiterima' => $selectedQuantityDiterima,
            'selectedSatuan'           => $selectedSatuan,
            'uniqueQuantityDiterima'   => $uniqueQuantityDiterima,
            'uniqueSatuan'             => $uniqueSatuan,
            'selectedHarga'            => $selectedHarga,
            'selectedJumlahHarga'      => $selectedJumlahHarga,
            'uniqueHarga'              => $uniqueHarga,
            'uniqueJumlahHarga'        => $uniqueJumlahHarga,
        ] );
    }
}
