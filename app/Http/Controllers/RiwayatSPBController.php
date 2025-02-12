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

    public function index ( $id )
    {
        $spb = SPB::with ( [ 
            'linkSpbDetailSpb.detailSpb.masterDataSparepart',
            'linkRkbSpbs.rkb.proyek',
            'masterDataSupplier',
        ] )->findOrFail ( $id );

        // Get unique values for filtering
        $uniqueValues = [ 
            'jenis_barang' => $spb->linkSpbDetailSpb
                ->pluck ( 'detailSpb.masterDataSparepart.nama' )
                ->unique ()
                ->values (),
            'merk'         => $spb->linkSpbDetailSpb
                ->pluck ( 'detailSpb.masterDataSparepart.merk' )
                ->unique ()
                ->values (),
            'spesifikasi'  => $spb->linkSpbDetailSpb
                ->pluck ( 'detailSpb.masterDataSparepart.part_number' )
                ->unique ()
                ->values (),
            'quantity'     => $spb->linkSpbDetailSpb
                ->pluck ( 'detailSpb.quantity_po' )
                ->unique ()
                ->values (),
            'satuan'       => $spb->linkSpbDetailSpb
                ->pluck ( 'detailSpb.satuan' )
                ->unique ()
                ->values (),
            'harga'        => $spb->linkSpbDetailSpb
                ->pluck ( 'detailSpb.harga' )
                ->unique ()
                ->sort ()
                ->values (),
            'jumlah_harga' => $spb->linkSpbDetailSpb
                ->map ( function ($item)
                {
                    return $item->detailSpb->quantity_po * $item->detailSpb->harga;
                } )
                ->unique ()
                ->sort ()
                ->values (),
        ];

        // Get selected values for each filter
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
            $filteredItems = $spb->linkSpbDetailSpb->filter ( function ($item) use ($selectedValues)
            {
                // Helper function to check if a field matches the filter
                $checkField = function ($value, $selectedValues)
                {
                    // If no filter values selected, show all
                    if ( empty ( $selectedValues ) )
                    {
                        return true;
                    }

                    // Check if value is empty/null
                    $isValueEmpty = empty ( $value ) || $value === '-' || $value === '';

                    // If 'null' is selected in filter
                    if ( in_array ( 'null', $selectedValues ) )
                    {
                        // Only return true if value is actually empty/null
                        if ( $isValueEmpty )
                        {
                            return true;
                        }
                    }

                    // Remove 'null' from selected values for non-empty value checking
                    $nonNullValues = array_filter ( $selectedValues, fn ( $v ) => $v !== 'null' );

                    // If there are non-null values selected, check if value matches any of them
                    if ( ! empty ( $nonNullValues ) )
                    {
                        return in_array ( (string) $value, $nonNullValues );
                    }

                    // If only 'null' was selected and value is not empty, return false
                    return false;
                };

                // Check each field against its filter
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

            // Create a new SPB instance with filtered items
            $filteredSpb = clone $spb;
            $filteredSpb->setRelation ( 'linkSpbDetailSpb', $filteredItems );
            $spb = $filteredSpb;
        }

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
