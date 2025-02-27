<?php

namespace App\Http\Controllers;

use App\Models\SPB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class RiwayatSPBController extends Controller
{
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];

        try
        {
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            \Log::error ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
    }

    private function getUniqueValues ( $spbId )
    {
        $spb = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataSparepart',
        ] )->findOrFail ( $spbId );

        $allItems = $spb->linkSpbDetailSpb;

        return [ 
            'jenis_barang' => $allItems->pluck ( 'detailSpb.masterDataSparepart.nama' )
                ->filter ()
                ->unique ()
                ->values (),
            'merk'         => $allItems->pluck ( 'detailSpb.masterDataSparepart.merk' )
                ->filter ()
                ->unique ()
                ->values (),
            'spesifikasi'  => $allItems->pluck ( 'detailSpb.masterDataSparepart.part_number' )
                ->filter ()
                ->unique ()
                ->values (),
            'quantity'     => $allItems->pluck ( 'detailSpb.quantity_po' )
                ->filter ()
                ->unique ()
                ->values (),
            'satuan'       => $allItems->pluck ( 'detailSpb.satuan' )
                ->filter ()
                ->unique ()
                ->values (),
            'harga'        => $allItems->pluck ( 'detailSpb.harga' )
                ->filter ()
                ->unique ()
                ->sort ()
                ->values (),
            'jumlah_harga' => $allItems->map ( function ($item)
            {
                return $item->detailSpb->quantity_po * $item->detailSpb->harga;
            } )
                ->filter ()
                ->unique ()
                ->sort ()
                ->values (),
        ];
    }

    public function index ( $id )
    {
        $spb = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataSparepart',
            'linkRkbSpbs.rkb.proyek',
            'masterDataSupplier',
        ] )->findOrFail ( $id );

        $items = $spb->linkSpbDetailSpb; // New variable holding initial items

        // Get selected values for each filter from request
        $selectedValues = [ 
            'jenis_barang' => $this->getSelectedValues ( request ( 'selected_jenis_barang' ) ),
            'merk'         => $this->getSelectedValues ( request ( 'selected_merk' ) ),
            'spesifikasi'  => $this->getSelectedValues ( request ( 'selected_spesifikasi' ) ),
            'quantity'     => $this->getSelectedValues ( request ( 'selected_quantity' ) ),
            'satuan'       => $this->getSelectedValues ( request ( 'selected_satuan' ) ),
            'harga'        => $this->getSelectedValues ( request ( 'selected_harga' ) ),
            'jumlah_harga' => $this->getSelectedValues ( request ( 'selected_jumlah_harga' ) ),
        ];

        // Apply filters if present
        if ( request ()->hasAny ( [ 'selected_jenis_barang', 'selected_merk', 'selected_spesifikasi', 'selected_quantity', 'selected_satuan', 'selected_harga', 'selected_jumlah_harga' ] ) )
        {
            $filteredItems = $items->filter ( function ($item) use ($selectedValues)
            {
                $checkField = function ($value, $selectedValues)
                {
                    if ( empty ( $selectedValues ) )
                    {
                        return true;
                    }
                    $isValueEmpty = empty ( $value ) || $value === '-' || $value === '';
                    if ( in_array ( 'null', $selectedValues ) )
                    {
                        if ( $isValueEmpty )
                        {
                            return true;
                        }
                    }
                    $nonNullValues = array_filter ( $selectedValues, fn ( $v ) => $v !== 'null' );
                    if ( ! empty ( $nonNullValues ) )
                    {
                        return in_array ( (string) $value, $nonNullValues );
                    }
                    return false;
                };

                $matchesJenisBarang = $checkField (
                    $item->detailSpb->masterDataSparepart->nama,
                    $selectedValues[ 'jenis_barang' ] ?? []
                );

                $matchesMerk = $checkField (
                    $item->detailSpb->masterDataSparepart->merk,
                    $selectedValues[ 'merk' ] ?? []
                );

                $matchesSpesifikasi = $checkField (
                    $item->detailSpb->masterDataSparepart->part_number,
                    $selectedValues[ 'spesifikasi' ] ?? []
                );

                $matchesQuantity = $checkField (
                    $item->detailSpb->quantity_po,
                    $selectedValues[ 'quantity' ] ?? []
                );

                $matchesSatuan = $checkField (
                    $item->detailSpb->satuan,
                    $selectedValues[ 'satuan' ] ?? []
                );

                $matchesHarga = $checkField (
                    $item->detailSpb->harga,
                    $selectedValues[ 'harga' ] ?? []
                );

                $jumlahHarga        = $item->detailSpb->quantity_po * $item->detailSpb->harga;
                $matchesJumlahHarga = $checkField (
                    $jumlahHarga,
                    $selectedValues[ 'jumlah_harga' ] ?? []
                );

                return $matchesJenisBarang && $matchesMerk && $matchesSpesifikasi &&
                    $matchesQuantity && $matchesSatuan && $matchesHarga && $matchesJumlahHarga;
            } );

            $filteredSpb = clone $spb;
            $filteredSpb->setRelation ( 'linkSpbDetailSpb', $filteredItems );
            $spb = $filteredSpb;
        }

        // Get unique values from the database regardless of current filters
        $uniqueValues = $this->getUniqueValues ( $id );

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

        // Create paginator manually from single SPB with sorting
        $TableData = new LengthAwarePaginator(
            collect ( [ $spb ] )
                ->sortByDesc ( 'updated_at' )
                ->sortByDesc ( 'id' ),  // items sorted
            1,                // total
            1,                // per page
            1                 // current page
        );

        return view ( 'dashboard.spb.riwayat.riwayat', [ 
            'proyeks'        => $proyeks,
            'TableData'      => $TableData,
            'headerPage'     => "SPB Supplier",
            'page'           => 'Riwayat SPB Supplier [' . $spb->linkRkbSpbs[ 0 ]->rkb->proyek->nama . ' | ' . $spb->linkRkbSpbs[ 0 ]->rkb->nomor . ']',
            'uniqueValues'   => $uniqueValues,
            'selectedValues' => $selectedValues,
        ] );
    }

    // Fungsi untuk mengekspor data SPB menjadi PDF
    public function exportPDF ( $id )
    {
        $spb = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataSparepart',
            'linkRkbSpbs',
            'masterDataSupplier',
        ] )->findOrFail ( $id );

        $totalHarga       = 0;
        $totalJumlahHarga = 0;

        foreach ( $spb->linkSpbDetailSpb as $item )
        {
            $totalHarga += $item->detailSpb->harga;
            $totalJumlahHarga += $item->detailSpb->quantity_po_po * $item->detailSpb->harga;
        }

        $ppn        = $totalJumlahHarga * 0.11;
        $grandTotal = $totalJumlahHarga + $ppn;

        $pdf = Pdf::loadView ( 'dashboard.spb.riwayat.partials.export-pdf', compact (
            'spb',
            'totalHarga',
            'totalJumlahHarga',
            'ppn',
            'grandTotal'
        ) );

        return $pdf->stream ( 'surat_pemesanan_barang.pdf' );
    }
}
