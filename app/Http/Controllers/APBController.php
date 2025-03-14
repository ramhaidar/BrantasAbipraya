<?php

namespace App\Http\Controllers;

use App\Models\APB;
use App\Models\ATB;
use App\Models\RKB;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\AlatProyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class APBController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showApbPage ( "Hutang Unit Alat", "Data APB EX Unit Alat", $request->id_proyek );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showApbPage ( "Panjar Unit Alat", "Data APB EX Panjar Unit Alat", $request->id_proyek );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showApbPage ( "Mutasi Proyek", "Data APB EX Mutasi Proyek", $request->id_proyek );
    }

    public function panjar_proyek ( Request $request )
    {
        return $this->showApbPage ( "Panjar Proyek", "Data APB EX Panjar Proyek", $request->id_proyek );
    }

    private function getBaseFilteredQuery ( $query, $excludeParam = null )
    {
        $request   = request ();
        $allParams = $request->all ();

        foreach ( $allParams as $param => $value )
        {
            // Only process selected_* parameters and skip the excluded one
            if ( strpos ( $param, 'selected_' ) === 0 && $param !== 'selected_' . $excludeParam )
            {
                // Clone the query and apply all filters except for the current parameter
                $tempRequest = new Request( $request->except ( $param ) );
                $query       = $this->applyFilters ( $query, $tempRequest );
            }
        }

        return $query;
    }

    /**
     * Extract unique values for all filterable fields
     * 
     * @param int $id_proyek Project ID
     * @param string $tipe APB type
     * @return array Associative array of unique values by field
     */
    private function getUniqueValues ( $id_proyek, $tipe )
    {
        // Create a new base query for all APB records of this type for this project
        $baseQuery = APB::where ( 'id_proyek', $id_proyek )
            ->where ( 'tipe', $tipe );

        // Get all APB IDs from the query to use in subqueries for better performance
        $apbIds = $baseQuery->pluck ( 'id' )->toArray ();
        if ( empty ( $apbIds ) )
        {
            return [ 
                'tanggal'            => [],
                'jenis_alat'         => [],
                'kode_alat'          => [],
                'merek_alat'         => [],
                'tipe_alat'          => [],
                'serial_number'      => [],
                'kode'               => [],
                'supplier'           => [],
                'sparepart'          => [],
                'merk'               => [],
                'part_number'        => [],
                'quantity'           => [],
                'satuan'             => [],
                'harga'              => [],
                'jumlah_harga'       => [],
                'mekanik'            => [],
                'status'             => [],
                'tujuan_proyek'      => [],
                'quantity_dikirim'   => [],
                'quantity_diterima'  => [],
                'quantity_digunakan' => [],
            ];
        }

        // Get unique dates in formatted form
        $dates = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'tanggal' )
            ->orderBy ( 'tanggal' )
            ->pluck ( 'tanggal' )
            ->map ( function ($date)
            {
                return date ( 'Y-m-d', strtotime ( $date ) );
            } )
            ->unique ()
            ->values ()
            ->toArray ();

        // Equipment-related fields (can be null for some types like mutasi-proyek)
        // Get unique jenis_alat
        $jenisAlat = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'id_alat_proyek' )
            ->with ( 'alatProyek.masterDataAlat' )
            ->get ()
            ->map ( function ($apb)
            {
                if ( $apb->alatProyek && $apb->alatProyek->masterDataAlat )
                {
                    return $apb->alatProyek->masterDataAlat->jenis_alat;
                }
                return null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique kode_alat
        $kodeAlat = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'id_alat_proyek' )
            ->with ( 'alatProyek.masterDataAlat' )
            ->get ()
            ->map ( function ($apb)
            {
                if ( $apb->alatProyek && $apb->alatProyek->masterDataAlat )
                {
                    return $apb->alatProyek->masterDataAlat->kode_alat;
                }
                return null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique merek_alat
        $merekAlat = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'id_alat_proyek' )
            ->with ( 'alatProyek.masterDataAlat' )
            ->get ()
            ->map ( function ($apb)
            {
                if ( $apb->alatProyek && $apb->alatProyek->masterDataAlat )
                {
                    return $apb->alatProyek->masterDataAlat->merek_alat;
                }
                return null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique tipe_alat
        $tipeAlat = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'id_alat_proyek' )
            ->with ( 'alatProyek.masterDataAlat' )
            ->get ()
            ->map ( function ($apb)
            {
                if ( $apb->alatProyek && $apb->alatProyek->masterDataAlat )
                {
                    return $apb->alatProyek->masterDataAlat->tipe_alat;
                }
                return null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique serial_number
        $serialNumber = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'id_alat_proyek' )
            ->with ( 'alatProyek.masterDataAlat' )
            ->get ()
            ->map ( function ($apb)
            {
                if ( $apb->alatProyek && $apb->alatProyek->masterDataAlat )
                {
                    return $apb->alatProyek->masterDataAlat->serial_number;
                }
                return null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique kategori sparepart combinations (kode: nama)
        $kategoriSpareparts = APB::whereIn ( 'id', $apbIds )
            ->whereHas ( 'masterDataSparepart.kategoriSparepart' )
            ->with ( 'masterDataSparepart.kategoriSparepart' )
            ->get ()
            ->map ( function ($apb)
            {
                if ( $apb->masterDataSparepart && $apb->masterDataSparepart->kategoriSparepart )
                {
                    $kat = $apb->masterDataSparepart->kategoriSparepart;
                    return $kat->kode . ': ' . $kat->nama;
                }
                return null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique suppliers
        $suppliers = APB::whereIn ( 'id', $apbIds )
            ->whereHas ( 'masterDataSupplier' )
            ->with ( 'masterDataSupplier' )
            ->get ()
            ->map ( function ($apb)
            {
                return $apb->masterDataSupplier->nama ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique spareparts
        $spareparts = APB::whereIn ( 'id', $apbIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($apb)
            {
                return $apb->masterDataSparepart->nama ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique merks
        $merks = APB::whereIn ( 'id', $apbIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($apb)
            {
                return $apb->masterDataSparepart->merk ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique part numbers
        $partNumbers = APB::whereIn ( 'id', $apbIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($apb)
            {
                return $apb->masterDataSparepart->part_number ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique quantities
        $quantities = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'quantity' )
            ->pluck ( 'quantity' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Get unique satuan values from Saldo
        $satuanValues = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'id_saldo' )
            ->with ( 'saldo' )
            ->get ()
            ->map ( function ($apb)
            {
                return $apb->saldo->satuan ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();
        sort ( $satuanValues );

        // Get unique harga values
        $hargaValues = APB::whereIn ( 'id', $apbIds )
            ->whereHas ( 'saldo' )
            ->with ( 'saldo' )
            ->get ()
            ->map ( function ($apb)
            {
                return $apb->saldo->harga ?? null;
            } )
            ->filter ()
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Calculate jumlah_harga
        $jumlahHargaValues = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'quantity' )
            ->whereHas ( 'saldo' )
            ->with ( 'saldo' )
            ->get ()
            ->map ( function ($apb)
            {
                return $apb->quantity * ( $apb->saldo->harga ?? 0 );
            } )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Get unique mekanik values
        $mekanik = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'mekanik' )
            ->pluck ( 'mekanik' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Get unique status values (plus special 'Empty/Null' for records with no status)
        $statusValues = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'status' )
            ->pluck ( 'status' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();
        // Always include these statuses
        $statusValues = array_unique ( array_merge ( $statusValues, [ 'pending', 'accepted', 'rejected' ] ) );
        sort ( $statusValues );

        // Get unique tujuan_proyek values
        $tujuanProyek = APB::whereIn ( 'id', $apbIds )
            ->whereNotNull ( 'id_tujuan_proyek' )
            ->with ( 'tujuanProyek' )
            ->get ()
            ->map ( function ($apb)
            {
                return $apb->tujuanProyek->nama ?? null;
            } )
            ->filter ()
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Values for special columns related to mutasi status
        // Sent quantity (quantity_dikirim)
        $quantityDikirim = APB::whereIn ( 'id', $apbIds )
            ->where ( 'tipe', 'mutasi-proyek' )
            ->whereNotNull ( 'status' )
            ->pluck ( 'quantity' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Received quantity (quantity_diterima)
        $quantityDiterima = APB::join ( 'atb', 'apb.id', '=', 'atb.id_apb_mutasi' )
            ->whereIn ( 'apb.id', $apbIds )
            ->where ( 'apb.tipe', 'mutasi-proyek' )
            ->whereNotNull ( 'atb.quantity' )
            ->pluck ( 'atb.quantity' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Used quantity (quantity_digunakan) - records without status
        $quantityDigunakan = APB::whereIn ( 'id', $apbIds )
            ->whereNull ( 'status' )
            ->pluck ( 'quantity' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        return [ 
            'tanggal'            => $dates,
            'jenis_alat'         => $jenisAlat,
            'kode_alat'          => $kodeAlat,
            'merek_alat'         => $merekAlat,
            'tipe_alat'          => $tipeAlat,
            'serial_number'      => $serialNumber,
            'kode'               => $kategoriSpareparts,
            'supplier'           => $suppliers,
            'sparepart'          => $spareparts,
            'merk'               => $merks,
            'part_number'        => $partNumbers,
            'satuan'             => $satuanValues,
            'quantity'           => $quantities,
            'harga'              => $hargaValues,
            'jumlah_harga'       => $jumlahHargaValues,
            'mekanik'            => $mekanik,
            'status'             => $statusValues,
            'tujuan_proyek'      => $tujuanProyek,
            'quantity_dikirim'   => $quantityDikirim,
            'quantity_diterima'  => $quantityDiterima,
            'quantity_digunakan' => $quantityDigunakan,
        ];
    }

    private function applyFilters ( $query, $request )
    {
        if ( $request->filled ( 'selected_tanggal' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_tanggal' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                // Handle Empty/Null case
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->orWhereNull ( 'apb.tanggal' );
                }

                // Split values into categories
                $exactDates    = [];
                $gtValue       = null;
                $ltValue       = null;
                $checkboxDates = [];

                // Categorize values
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' )
                    {
                        continue;
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactDates[] = substr ( $value, 6 );
                    }
                    elseif ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $gtValue = substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $ltValue = substr ( $value, 3 );
                    }
                    else
                    {
                        $checkboxDates[] = $value;
                    }
                }

                // Apply date range if both gt and lt exist
                if ( $gtValue && $ltValue )
                {
                    $q->orWhere ( function ($rangeQ) use ($gtValue, $ltValue)
                    {
                        $rangeQ->whereRaw ( "DATE(apb.tanggal) BETWEEN ? AND ?", [ $gtValue, $ltValue ] );
                    } );
                }
                else
                {
                    // Apply individual conditions if not a range
                    if ( $gtValue )
                    {
                        $q->orWhereRaw ( "DATE(apb.tanggal) >= ?", [ $gtValue ] );
                    }
                    if ( $ltValue )
                    {
                        $q->orWhereRaw ( "DATE(apb.tanggal) <= ?", [ $ltValue ] );
                    }
                }

                // Apply exact dates
                foreach ( $exactDates as $date )
                {
                    $q->orWhereRaw ( "DATE(apb.tanggal) = ?", [ $date ] );
                }

                // Apply checkbox selected dates
                foreach ( $checkboxDates as $date )
                {
                    $q->orWhereRaw ( "DATE(apb.tanggal) = ?", [ $date ] );
                }
            } );
        }

        if ( $request->filled ( 'selected_jenis_alat' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_jenis_alat' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'jenis_alat' )
                                ->orWhere ( 'jenis_alat', '' )
                                ->orWhere ( 'jenis_alat', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'jenis_alat', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'jenis_alat', $selectedValues );
                    } );
                }
            } );
        }
        if ( $request->filled ( 'selected_kode_alat' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_kode_alat' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'kode_alat' )
                                ->orWhere ( 'kode_alat', '' )
                                ->orWhere ( 'kode_alat', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'kode_alat', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'kode_alat', $selectedValues );
                    } );
                }
            } );
        }
        if ( $request->filled ( 'selected_merek_alat' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_merek_alat' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'merek_alat' )
                                ->orWhere ( 'merek_alat', '' )
                                ->orWhere ( 'merek_alat', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'merek_alat', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'merek_alat', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_tipe_alat' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_tipe_alat' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'tipe_alat' )
                                ->orWhere ( 'tipe_alat', '' )
                                ->orWhere ( 'tipe_alat', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'tipe_alat', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'tipe_alat', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_serial_number' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_serial_number' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'alatProyek.masterDataAlat' )
                        ->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq)
                        {
                            $sq->whereNull ( 'serial_number' )
                                ->orWhere ( 'serial_number', '' )
                                ->orWhere ( 'serial_number', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'serial_number', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'alatProyek.masterDataAlat', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'serial_number', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_kode' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_kode' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSparepart.kategoriSparepart' )
                        ->orWhereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq)
                        {
                            $sq->whereNull ( 'kode' )
                                ->orWhere ( 'kode', '' )
                                ->orWhere ( 'kode', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( DB::raw ( "CONCAT(kode, ': ', nama)" ), $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( DB::raw ( "CONCAT(kode, ': ', nama)" ), $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_supplier' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_supplier' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSupplier' )
                        ->orWhereHas ( 'masterDataSupplier', function ($sq)
                        {
                            $sq->whereNull ( 'nama' )
                                ->orWhere ( 'nama', '' )
                                ->orWhere ( 'nama', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSupplier', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'nama', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSupplier', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'nama', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_sparepart' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_sparepart' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSparepart' )
                        ->orWhereHas ( 'masterDataSparepart', function ($sq)
                        {
                            $sq->whereNull ( 'nama' )
                                ->orWhere ( 'nama', '' )
                                ->orWhere ( 'nama', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSparepart', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'nama', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'nama', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_merk' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_merk' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSparepart' )
                        ->orWhereHas ( 'masterDataSparepart', function ($sq)
                        {
                            $sq->whereNull ( 'merk' )
                                ->orWhere ( 'merk', '' )
                                ->orWhere ( 'merk', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSparepart', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'merk', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'merk', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_merk' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_merk' ) );
            $query->whereHas ( 'masterDataSparepart', function ($q) use ($selectedValues)
            {
                $q->whereIn ( 'merk', $selectedValues );
            } );
        }

        if ( $request->filled ( 'selected_part_number' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_part_number' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'masterDataSparepart' )
                        ->orWhereHas ( 'masterDataSparepart', function ($sq)
                        {
                            $sq->whereNull ( 'part_number' )
                                ->orWhere ( 'part_number', '' )
                                ->orWhere ( 'part_number', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'masterDataSparepart', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'part_number', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'part_number', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' )
                    {
                        $q->orWhereNull ( 'apb.quantity' )
                            ->orWhere ( 'apb.quantity', 0 );
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValue = (int) substr ( $value, 6 );
                        $q->orWhere ( 'apb.quantity', '=', $exactValue );
                    }
                    elseif ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $gtValue = (int) substr ( $value, 3 );
                        $q->orWhere ( 'apb.quantity', '>=', $gtValue );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $ltValue = (int) substr ( $value, 3 );
                        $q->orWhere ( 'apb.quantity', '<=', $ltValue );
                    }
                    elseif ( is_numeric ( $value ) )
                    {
                        $q->orWhere ( 'apb.quantity', '=', (int) $value );
                    }
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity_dikirim' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity_dikirim' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                // Track range conditions
                $rangeConditions = [ 
                    'gt' => null,
                    'lt' => null,
                ];

                // Process each value
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' )
                    {
                        $q->where ( function ($sq)
                        {
                            $sq->whereNull ( 'status' ) // Not a mutasi record
                                ->orWhere ( 'apb.quantity', '0' )
                                ->orWhereNull ( 'apb.quantity' );
                        } );
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValue = substr ( $value, 6 );
                        if ( is_numeric ( $exactValue ) )
                        {
                            $q->orWhere ( function ($sq) use ($exactValue)
                            {
                                $sq->whereNotNull ( 'status' ) // Only for records with status (mutasi)
                                    ->where ( 'apb.quantity', $exactValue );
                            } );
                        }
                    }
                    elseif ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $rangeConditions[ 'gt' ] = substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $rangeConditions[ 'lt' ] = substr ( $value, 3 );
                    }
                    elseif ( is_numeric ( $value ) )
                    { // Handle checkbox values
                        $q->orWhere ( function ($sq) use ($value)
                        {
                            $sq->whereNotNull ( 'status' ) // Only for records with status (mutasi)
                                ->where ( 'apb.quantity', $value );
                        } );
                    }
                }

                // Apply range conditions if they exist
                if ( $rangeConditions[ 'gt' ] || $rangeConditions[ 'lt' ] )
                {
                    $q->orWhere ( function ($rangeQ) use ($rangeConditions)
                    {
                        $rangeQ->whereNotNull ( 'status' ); // Only for records with status (mutasi)
                        if ( $rangeConditions[ 'gt' ] )
                        {
                            $rangeQ->where ( 'apb.quantity', '>=', $rangeConditions[ 'gt' ] );
                        }
                        if ( $rangeConditions[ 'lt' ] )
                        {
                            $rangeQ->where ( 'apb.quantity', '<=', $rangeConditions[ 'lt' ] );
                        }
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity_diterima' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity_diterima' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                // Track range conditions and checkbox values
                $rangeConditions = [ 
                    'gt' => null,
                    'lt' => null,
                ];
                $exactValues     = [];
                $checkboxValues  = [];
                $hasEmptyNull    = false;

                // Categorize selected values
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' )
                    {
                        $hasEmptyNull = true;
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValues[] = (int) substr ( $value, 6 );
                    }
                    elseif ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $rangeConditions[ 'gt' ] = (int) substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $rangeConditions[ 'lt' ] = (int) substr ( $value, 3 );
                    }
                    elseif ( is_numeric ( $value ) )
                    {
                        $checkboxValues[] = (int) $value;
                    }
                }

                // Handle Empty/Null case if present
                if ( $hasEmptyNull )
                {
                    $q->orWhere ( function ($sq)
                    {
                        $sq->whereNull ( 'status' )
                            ->orWhereDoesntHave ( 'atbMutasi' )
                            ->orWhereHas ( 'atbMutasi', function ($ssq)
                            {
                                $ssq->whereNull ( 'quantity' )
                                    ->orWhere ( 'quantity', '0' );
                            } );
                    } );
                }

                // Handle checkbox values if present
                if ( ! empty ( $checkboxValues ) )
                {
                    $q->orWhereHas ( 'atbMutasi', function ($sq) use ($checkboxValues)
                    {
                        $sq->whereIn ( 'quantity', $checkboxValues );
                    } );
                }

                // Apply exact values if they exist
                if ( ! empty ( $exactValues ) )
                {
                    $q->orWhereHas ( 'atbMutasi', function ($sq) use ($exactValues)
                    {
                        $sq->whereIn ( 'quantity', $exactValues );
                    } );
                }

                // Apply range conditions if they exist
                if ( $rangeConditions[ 'gt' ] !== null || $rangeConditions[ 'lt' ] !== null )
                {
                    $q->orWhereHas ( 'atbMutasi', function ($rangeQ) use ($rangeConditions)
                    {
                        if ( $rangeConditions[ 'gt' ] !== null )
                        {
                            $rangeQ->where ( 'quantity', '>=', $rangeConditions[ 'gt' ] );
                        }
                        if ( $rangeConditions[ 'lt' ] !== null )
                        {
                            $rangeQ->where ( 'quantity', '<=', $rangeConditions[ 'lt' ] );
                        }
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity_digunakan' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity_digunakan' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                $hasEmptyNull    = in_array ( 'Empty/Null', $selectedValues );
                $hasRangeOrExact = false;

                // Initialize conditions
                $rangeConditions = [ 
                    'gt' => null,
                    'lt' => null,
                ];
                $exactValues     = [];

                // Collect all non-Empty/Null conditions
                foreach ( $selectedValues as $value )
                {
                    if ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $rangeConditions[ 'gt' ] = (int) substr ( $value, 3 );
                        $hasRangeOrExact         = true;
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $rangeConditions[ 'lt' ] = (int) substr ( $value, 3 );
                        $hasRangeOrExact         = true;
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValues[]   = (int) substr ( $value, 6 );
                        $hasRangeOrExact = true;
                    }
                    elseif ( is_numeric ( $value ) )
                    {
                        $exactValues[]   = (int) $value;
                        $hasRangeOrExact = true;
                    }
                }

                // Handle Empty/Null case
                if ( $hasEmptyNull )
                {
                    $q->orWhere ( function ($sq)
                    {
                        $sq->whereNotNull ( 'status' )
                            ->orWhere ( function ($ssq)
                            {
                                $ssq->whereNull ( 'status' )
                                    ->where ( function ($sssq)
                                    {
                                        $sssq->whereNull ( 'apb.quantity' )
                                            ->orWhere ( 'apb.quantity', 0 );
                                    } );
                            } );
                    } );
                }

                // Handle range and exact values
                if ( $hasRangeOrExact )
                {
                    $q->orWhere ( function ($sq) use ($rangeConditions, $exactValues)
                    {
                        $sq->whereNull ( 'status' )->where ( function ($ssq) use ($rangeConditions, $exactValues)
                        {
                            // Handle exact values
                            if ( ! empty ( $exactValues ) )
                            {
                                $ssq->whereIn ( 'apb.quantity', $exactValues );
                            }

                            // Handle range conditions
                            if ( $rangeConditions[ 'gt' ] !== null || $rangeConditions[ 'lt' ] !== null )
                            {
                                $ssq->orWhere ( function ($rangeQ) use ($rangeConditions)
                                {
                                    if ( $rangeConditions[ 'gt' ] !== null )
                                    {
                                        $rangeQ->where ( 'apb.quantity', '>=', $rangeConditions[ 'gt' ] );
                                    }
                                    if ( $rangeConditions[ 'lt' ] !== null )
                                    {
                                        $rangeQ = $rangeQ->where ( 'apb.quantity', '<=', $rangeConditions[ 'lt' ] );
                                    }
                                } );
                            }
                        } );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_satuan' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_satuan' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereDoesntHave ( 'saldo' )
                        ->orWhereHas ( 'saldo', function ($sq)
                        {
                            $sq->whereNull ( 'satuan' )
                                ->orWhere ( 'satuan', '' )
                                ->orWhere ( 'satuan', '-' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'saldo', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'satuan', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'saldo', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'satuan', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_jumlah_harga' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_jumlah_harga' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                // Track range conditions and other types of values
                $rangeConditions = [ 
                    'gt' => null,
                    'lt' => null,
                ];
                $exactValue      = null;
                $checkboxValues  = [];

                // Process each value
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' )
                    {
                        $q->where ( function ($sq)
                        {
                            $sq->whereNull ( 'apb.quantity' )
                                ->orWhere ( 'apb.quantity', 0 )
                                ->orWhereDoesntHave ( 'saldo' )
                                ->orWhereHas ( 'saldo', function ($ssq)
                                {
                                    $ssq->whereNull ( 'harga' )
                                        ->orWhere ( 'harga', 0 );
                                } );
                        } );
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValue = (int) substr ( $value, 6 );
                        $q->orWhereHas ( 'saldo', function ($sq) use ($exactValue)
                        {
                            $sq->whereRaw ( '(saldo.harga * apb.quantity) = ?', [ $exactValue ] );
                        } );
                    }
                    elseif ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $rangeConditions[ 'gt' ] = (int) substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $rangeConditions[ 'lt' ] = (int) substr ( $value, 3 );
                    }
                    elseif ( is_numeric ( $value ) ) // Handle checkbox values
                    {
                        // Apply checkbox value immediately using the same calculation as exact match
                        $checkboxValue = (int) $value;
                        $q->orWhereHas ( 'saldo', function ($sq) use ($checkboxValue)
                        {
                            $sq->whereRaw ( '(saldo.harga * apb.quantity) = ?', [ $checkboxValue ] );
                        } );
                    }
                }

                // Apply range conditions if they exist
                if ( $rangeConditions[ 'gt' ] || $rangeConditions[ 'lt' ] )
                {
                    $q->orWhereHas ( 'saldo', function ($rangeQ) use ($rangeConditions)
                    {
                        if ( $rangeConditions[ 'gt' ] )
                        {
                            $rangeQ->whereRaw ( '(saldo.harga * apb.quantity) >= ?', [ $rangeConditions[ 'gt' ] ] );
                        }
                        if ( $rangeConditions[ 'lt' ] )
                        {
                            $rangeQ->whereRaw ( '(saldo.harga * apb.quantity) <= ?', [ $rangeConditions[ 'lt' ] ] );
                        }
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_harga' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_harga' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                // Track range conditions and other types of values
                $rangeConditions = [ 
                    'gt' => null,
                    'lt' => null,
                ];
                $exactValue      = null;
                $checkboxValues  = [];

                // Process each value
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' )
                    {
                        $q->orWhereDoesntHave ( 'saldo' )
                            ->orWhereHas ( 'saldo', function ($sq)
                            {
                                $sq->whereNull ( 'harga' )
                                    ->orWhere ( 'harga', 0 );
                            } );
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValue = substr ( $value, 6 );
                    }
                    elseif ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $rangeConditions[ 'gt' ] = substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $rangeConditions[ 'lt' ] = substr ( $value, 3 );
                    }
                    elseif ( is_numeric ( $value ) ) // Handle checkbox values
                    {
                        $checkboxValues[] = (int) $value;
                    }
                }

                // Apply exact value if exists
                if ( $exactValue !== null && is_numeric ( $exactValue ) )
                {
                    $q->orWhereHas ( 'saldo', function ($sq) use ($exactValue)
                    {
                        $sq->where ( 'harga', $exactValue );
                    } );
                }

                // Apply checkbox values if exist
                if ( ! empty ( $checkboxValues ) )
                {
                    $q->orWhereHas ( 'saldo', function ($sq) use ($checkboxValues)
                    {
                        $sq->whereIn ( 'harga', $checkboxValues );
                    } );
                }

                // Apply range conditions if they exist
                if ( $rangeConditions[ 'gt' ] || $rangeConditions[ 'lt' ] )
                {
                    $q->orWhereHas ( 'saldo', function ($rangeQ) use ($rangeConditions)
                    {
                        if ( $rangeConditions[ 'gt' ] )
                        {
                            $rangeQ->where ( 'harga', '>=', $rangeConditions[ 'gt' ] );
                        }
                        if ( $rangeConditions[ 'lt' ] )
                        {
                            $rangeQ->where ( 'harga', '<=', $rangeConditions[ 'lt' ] );
                        }
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_mekanik' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_mekanik' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereNull ( 'mekanik' )
                        ->orWhere ( 'mekanik', '' )
                        ->orWhere ( 'mekanik', '-' );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereIn ( 'mekanik', $otherValues );
                    }
                }
                else
                {
                    $q->whereIn ( 'mekanik', $selectedValues );
                }
            } );
        }

        if ( $request->filled ( 'selected_tujuan_proyek' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_tujuan_proyek' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                if ( in_array ( 'Empty/Null', $selectedValues ) )
                {
                    $q->whereNull ( 'id_tujuan_proyek' )
                        ->orWhereHas ( 'tujuanProyek', function ($sq)
                        {
                            $sq->whereNull ( 'nama' )->orWhere ( 'nama', '' );
                        } );

                    $otherValues = array_diff ( $selectedValues, [ 'Empty/Null' ] );
                    if ( ! empty ( $otherValues ) )
                    {
                        $q->orWhereHas ( 'tujuanProyek', function ($sq) use ($otherValues)
                        {
                            $sq->whereIn ( 'nama', $otherValues );
                        } );
                    }
                }
                else
                {
                    $q->whereHas ( 'tujuanProyek', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'nama', $selectedValues );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity_diterima' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity_diterima' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                // Track range conditions and checkbox values
                $rangeConditions = [ 
                    'gt' => null,
                    'lt' => null,
                ];
                $exactValues     = [];
                $checkboxValues  = [];
                $hasEmptyNull    = false;

                // Categorize selected values
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' )
                    {
                        $hasEmptyNull = true;
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValues[] = (int) substr ( $value, 6 );
                    }
                    elseif ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $rangeConditions[ 'gt' ] = (int) substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $rangeConditions[ 'lt' ] = (int) substr ( $value, 3 );
                    }
                    elseif ( is_numeric ( $value ) )
                    {
                        $checkboxValues[] = (int) $value;
                    }
                }

                // Handle Empty/Null case if present
                if ( $hasEmptyNull )
                {
                    $q->orWhere ( function ($sq)
                    {
                        $sq->whereNull ( 'status' )
                            ->orWhereDoesntHave ( 'atbMutasi' )
                            ->orWhereHas ( 'atbMutasi', function ($ssq)
                            {
                                $ssq->whereNull ( 'quantity' )
                                    ->orWhere ( 'quantity', '0' );
                            } );
                    } );
                }

                // Handle checkbox values if present
                if ( ! empty ( $checkboxValues ) )
                {
                    $q->orWhereHas ( 'atbMutasi', function ($sq) use ($checkboxValues)
                    {
                        $sq->whereIn ( 'quantity', $checkboxValues );
                    } );
                }

                // Apply exact values if they exist
                if ( ! empty ( $exactValues ) )
                {
                    $q->orWhereHas ( 'atbMutasi', function ($sq) use ($exactValues)
                    {
                        $sq->whereIn ( 'quantity', $exactValues );
                    } );
                }

                // Apply range conditions if they exist
                if ( $rangeConditions[ 'gt' ] !== null || $rangeConditions[ 'lt' ] !== null )
                {
                    $q->orWhereHas ( 'atbMutasi', function ($rangeQ) use ($rangeConditions)
                    {
                        if ( $rangeConditions[ 'gt' ] !== null )
                        {
                            $rangeQ->where ( 'quantity', '>=', $rangeConditions[ 'gt' ] );
                        }
                        if ( $rangeConditions[ 'lt' ] !== null )
                        {
                            $rangeQ->where ( 'quantity', '<=', $rangeConditions[ 'lt' ] );
                        }
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_quantity_digunakan' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_quantity_digunakan' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                $hasEmptyNull    = in_array ( 'Empty/Null', $selectedValues );
                $hasRangeOrExact = false;

                // Initialize conditions
                $rangeConditions = [ 
                    'gt' => null,
                    'lt' => null,
                ];
                $exactValues     = [];

                // Collect all non-Empty/Null conditions
                foreach ( $selectedValues as $value )
                {
                    if ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $rangeConditions[ 'gt' ] = (int) substr ( $value, 3 );
                        $hasRangeOrExact         = true;
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $rangeConditions[ 'lt' ] = (int) substr ( $value, 3 );
                        $hasRangeOrExact         = true;
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $exactValues[]   = (int) substr ( $value, 6 );
                        $hasRangeOrExact = true;
                    }
                    elseif ( is_numeric ( $value ) )
                    {
                        $exactValues[]   = (int) $value;
                        $hasRangeOrExact = true;
                    }
                }

                // Handle Empty/Null case
                if ( $hasEmptyNull )
                {
                    $q->orWhere ( function ($sq)
                    {
                        $sq->whereNotNull ( 'status' )
                            ->orWhere ( function ($ssq)
                            {
                                $ssq->whereNull ( 'status' )
                                    ->where ( function ($sssq)
                                    {
                                        $sssq->whereNull ( 'apb.quantity' )
                                            ->orWhere ( 'apb.quantity', 0 );
                                    } );
                            } );
                    } );
                }

                // Handle range and exact values
                if ( $hasRangeOrExact )
                {
                    $q->orWhere ( function ($sq) use ($rangeConditions, $exactValues)
                    {
                        $sq->whereNull ( 'status' )->where ( function ($ssq) use ($rangeConditions, $exactValues)
                        {
                            // Handle exact values
                            if ( ! empty ( $exactValues ) )
                            {
                                $ssq->whereIn ( 'apb.quantity', $exactValues );
                            }

                            // Handle range conditions
                            if ( $rangeConditions[ 'gt' ] !== null || $rangeConditions[ 'lt' ] !== null )
                            {
                                $ssq->orWhere ( function ($rangeQ) use ($rangeConditions)
                                {
                                    if ( $rangeConditions[ 'gt' ] !== null )
                                    {
                                        $rangeQ->where ( 'apb.quantity', '>=', $rangeConditions[ 'gt' ] );
                                    }
                                    if ( $rangeConditions[ 'lt' ] !== null )
                                    {
                                        $rangeQ = $rangeQ->where ( 'apb.quantity', '<=', $rangeConditions[ 'lt' ] );
                                    }
                                } );
                            }
                        } );
                    } );
                }
            } );
        }

        if ( $request->filled ( 'selected_status' ) )
        {
            $selectedValues = $this->getSelectedValues ( $request->get ( 'selected_status' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' || $value === 'Penggunaan' )
                    {
                        $q->orWhereNull ( 'status' );
                    }
                    else
                    {
                        $q->orWhere ( 'status', strtolower ( $value ) );
                    }
                }
            } );
        }

        return $query;
    }
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

    private function showApbPage ( $tipe, $pageTitle, $id_proyek )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) request ()->get ( 'per_page' ), $allowedPerPage ) ? (int) request ()->get ( 'per_page' ) : 10;
        $tipe           = strtolower ( str_replace ( ' ', '-', $tipe ) );
        $search         = request ()->get ( 'search', '' );
        $query          = APB::with ( [ 'masterDataSparepart.kategoriSparepart', 'masterDataSupplier', 'proyek', 'alatProyek.masterDataAlat', 'saldo' ] )
            ->select ( 'apb.*' ) // Tambahkan ini
            ->where ( 'apb.id_proyek', $id_proyek )
            ->where ( 'apb.tipe', $tipe );
        if ( $search )
        {
            $query->where ( function ($q) use ($search)
            {
                $searchLower    = strtolower ( trim ( $search ) );
                $searchParts    = explode ( ' ', $searchLower );
                $hariIndonesia  = [ 'senin' => 'Monday', 'selasa' => 'Tuesday', 'rabu' => 'Wednesday', 'kamis' => 'Thursday', 'jumat' => 'Friday', "jum'at" => 'Friday', 'sabtu' => 'Saturday', 'minggu' => 'Sunday' ];
                $bulanIndonesia = [ 'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04', 'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08', 'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12' ];
                $isDateSearch   = false;
                $year           = null;
                $month          = null;
                $day            = null;
                foreach ( $searchParts as $part )
                {
                    if ( is_numeric ( $part ) && strlen ( $part ) === 4 )
                    {
                        $year         = $part;
                        $isDateSearch = true;
                        continue;
                    }
                    foreach ( $hariIndonesia as $indo => $eng )
                    {
                        if ( str_starts_with ( $indo, $part ) )
                        {
                            $isDateSearch = true;
                            $q->orWhereRaw ( "DAYNAME(tanggal) = ?", [ $eng ] );
                            break 2;
                        }
                    }
                    foreach ( $bulanIndonesia as $indo => $num )
                    {
                        if ( str_starts_with ( $indo, $part ) )
                        {
                            $month        = $num;
                            $isDateSearch = true;
                            break;
                        }
                    }
                    if ( is_numeric ( $part ) && strlen ( $part ) <= 2 )
                    {
                        $day          = sprintf ( "%02d", $part );
                        $isDateSearch = true;
                    }
                }
                if ( $isDateSearch )
                {
                    if ( $year )
                    {
                        $q->whereYear ( 'tanggal', $year );
                    }
                    if ( $month )
                    {
                        $q->whereMonth ( 'tanggal', $month );
                    }
                    if ( $day )
                    {
                        $q->whereDay ( 'tanggal', $day );
                    }
                }
                else
                {
                    $q->where ( function ($q) use ($search)
                    {
                        $q->whereHas ( 'masterDataSparepart', function ($q) use ($search)
                        {
                            $q->where ( 'nama', 'ilike', "%{$search}%" )->orWhere ( 'part_number', 'ilike', "%{$search}%" )->orWhere ( 'merk', 'ilike', "%{$search}%" )->orWhereHas ( 'kategoriSparepart', function ($q) use ($search)
                            {
                                $q->where ( 'kode', 'ilike', "%{$search}%" )->orWhere ( 'nama', 'ilike', "%{$search}%" );
                            } );
                        } )->orWhereHas ( 'masterDataSupplier', function ($q) use ($search)
                        {
                            $q->where ( 'nama', 'ilike', "%{$search}%" );
                        } )->orWhereHas ( 'alatProyek.masterDataAlat', function ($q) use ($search)
                        {
                            $q->where ( 'jenis_alat', 'ilike', "%{$search}%" )->orWhere ( 'kode_alat', 'ilike', "%{$search}%" )->orWhere ( 'merek_alat', 'ilike', "%{$search}%" )->orWhere ( 'tipe_alat', 'ilike', "%{$search}%" )->orWhere ( 'serial_number', 'ilike', "%{$search}%" );
                        } )->orWhereHas ( 'tujuanProyek', function ($q) use ($search)
                        {
                            $q->where ( 'nama', 'ilike', "%{$search}%" );
                        } )->orWhereHas ( 'saldo', function ($q) use ($search)
                        {
                            $q->where ( 'satuan', 'ilike', "%{$search}%" );
                        } )->orWhere ( 'mekanik', 'ilike', "%{$search}%" );
                        if ( is_numeric ( str_replace ( [ ',', '.' ], '', $search ) ) )
                        {
                            $numericSearch = str_replace ( [ ',', '.' ], '', $search );
                            $q->orWhere ( 'quantity', 'ilike', "%{$numericSearch}%" )->orWhereHas ( 'saldo', function ($q) use ($numericSearch)
                            {
                                $q->where ( 'harga', 'ilike', "%{$numericSearch}%" );
                            } )->orWhereRaw ( '(
                                  SELECT s.harga * apb.quantity
                                  FROM saldo s
                                  WHERE s.id = apb.id_saldo
                              ) LIKE ?', [ "%{$numericSearch}%" ] );
                        }
                    } );
                }
            } );
        }
        $uniqueValues = $this->getUniqueValues ( $id_proyek, $tipe );
        $query        = $this->applyFilters ( $query, request () );
        $totalQuery   = clone $query;
        $totalAmount  = $totalQuery->join ( 'saldo', 'apb.id_saldo', '=', 'saldo.id' )
            ->where ( function ($q)
            {
                $q->where ( 'apb.status', 'accepted' )
                    ->orWhereNull ( 'apb.status' ); // Include null status (Penggunaan)
// Explicitly exclude 'pending' and 'rejected' status
            } )
            ->sum ( DB::raw ( 'apb.quantity * saldo.harga' ) );

        $proyek              = Proyek::with ( "users" )->findOrFail ( $id_proyek );
        $alats               = AlatProyek::where ( 'id_proyek', $id_proyek )->get ();
        $spbs                = $this->getFilteredSpbs ( $id_proyek );
        $spareparts          = Saldo::where ( 'quantity', '>', 0 )->with ( [ 'masterDataSparepart', 'atb' ] )->whereHas ( 'atb', function ($query) use ($tipe)
        {
            $query->where ( 'tipe', $tipe );
        } )->whereHas ( 'atb', function ($query) use ($id_proyek)
        {
            $query->where ( 'id_proyek', $id_proyek );
        } )->get ()->sortBy ( 'atb.tanggal' );
        $sparepartsForMutasi = Saldo::where ( 'quantity', '>', 0 )->with ( [ 'masterDataSparepart', 'atb' ] )->whereHas ( 'atb', function ($query) use ($tipe, $id_proyek)
        {
            $query->where ( 'id_proyek', $id_proyek );
            if ( $tipe !== 'mutasi-proyek' )
            {
                $query->where ( 'tipe', $tipe );
            }
        } )->get ()->sortBy ( 'atb.tanggal' );

        // Add master data alats for bypass modal
        $masterDataAlats = MasterDataAlat::orderBy ( 'jenis_alat' )->orderBy ( 'kode_alat' )->get ();

        $TableData               = $query->orderBy ( 'tanggal', 'desc' )->orderBy ( 'apb.updated_at', 'desc' )->orderBy ( 'apb.id', 'desc' )->paginate ( $perPage )->withQueryString ();
        $TableData->total_amount = $totalAmount;
        $user                    = Auth::user ();
        $proyeksQuery            = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }
        $proyeks = $proyeksQuery->orderBy ( "updated_at", "desc" )->orderBy ( "id", "desc" )->get ();
        return view ( "dashboard.apb.apb", [ 
            "proyek"              => $proyek,
            "alats"               => $alats,
            "proyeks"             => $proyeks,
            "spbs"                => $spbs,
            "spareparts"          => $spareparts,
            "sparepartsForMutasi" => $sparepartsForMutasi,
            "headerPage"          => $proyek->nama,
            "page"                => $pageTitle,
            "tipe"                => $tipe,
            "TableData"           => $TableData,
            "search"              => $search,
            "uniqueValues"        => $uniqueValues,
            "masterDataAlats"     => $masterDataAlats, // Added for bypass modal
        ] );
    }

    private function getFilteredSpbs ( $id_proyek )
    {
        $rkbs = RKB::with ( "spbs.linkSpbDetailSpb.detailSpb" )->where ( 'id_proyek', $id_proyek )->get ();
        $spbs = collect ();
        foreach ( $rkbs as $rkb )
        {
            $filteredSpbs = $rkb->spbs->filter ( function ($spb)
            {
                $hasRemainingQuantity = $spb->linkSpbDetailSpb->some ( function ($link)
                {
                    return $link->detailSpb->quantity_belum_diterima > 0;
                } );
                return $hasRemainingQuantity && ( ( ! $spb->is_addendum && ! isset ( $spb->id_spb_original ) ) || ( $spb->is_addendum && isset ( $spb->id_spb_original ) ) );
            } );
            $spbs         = $spbs->merge ( $filteredSpbs );
        }
        return $spbs;
    }
    public function store ( Request $request )
    {
        $validated = $request->validate ( [ 
            'tanggal'   => [ 'required', 'date' ],
            'id_proyek' => [ 'required', 'exists:proyek,id' ],
            'id_alat'   => [ 'required', 'exists:alat_proyek,id' ],
            'id_saldo'  => [ 'required', 'exists:saldo,id' ],
            'quantity'  => [ 'required', 'integer', 'min:1' ],
            'tipe'      => [ 'required', 'string' ],
            'mekanik'   => [ 'required', 'string', 'max:255' ]
        ] );

        try
        {
            DB::beginTransaction ();
            $saldo               = Saldo::find ( $request->id_saldo );
            $masterDataSparepart = $saldo->masterDataSparepart;
            if ( $saldo->quantity < $request->quantity )
            {
                throw new \Exception( 'Stok sparepart tidak mencukupi.' );
            }
            $apb = APB::create ( [ 'tanggal' => $request->tanggal, 'tipe' => $request->tipe, 'mekanik' => $request->mekanik, 'quantity' => $request->quantity, 'id_saldo' => $saldo->id, 'id_proyek' => $request->id_proyek, 'id_master_data_sparepart' => $masterDataSparepart->id, 'id_master_data_supplier' => $saldo->id_master_data_supplier, 'id_alat_proyek' => $request->id_alat ] );
            $saldo->decrementQuantity ( $request->quantity );
            DB::commit ();
            return redirect ()->back ()->with ( 'success', 'Data APB berhasil ditambahkan.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal menambahkan data APB: ' . $e->getMessage () );
        }
    }

    /**
     * Store a new APB entry using the bypass method
     * The bypass allows selecting equipment from master data instead of project equipment
     */
    public function store_bypass ( Request $request )
    {
        $validated = $request->validate ( [ 
            'tanggal'             => [ 'required', 'date' ],
            'id_proyek'           => [ 'required', 'exists:proyek,id' ],
            'id_master_data_alat' => [ 'required' ],
            'id_saldo'            => [ 'required', 'exists:saldo,id' ],
            'quantity'            => [ 'required', 'integer', 'min:1' ],
            'tipe'                => [ 'required', 'string' ],
            'mekanik'             => [ 'required', 'string', 'max:255' ]
        ] );

        try
        {
            DB::beginTransaction ();

            // Get the saldo and master data sparepart
            $saldo               = Saldo::find ( $request->id_saldo );
            $masterDataSparepart = $saldo->masterDataSparepart;

            // Check if quantity is available
            if ( $saldo->quantity < $request->quantity )
            {
                throw new \Exception( 'Stok sparepart tidak mencukupi.' );
            }

            // Find or create AlatProyek for the given master data alat
            $alatProyek = null;
            if ( $request->id_master_data_alat === 'workshop' )
            {
                // For workshop, find or create a workshop entry
                $masterDataAlat = MasterDataAlat::firstOrCreate (
                    [ 'jenis_alat' => 'Workshop' ],
                    [ 
                        'kode_alat'     => 'WS',
                        'merek_alat'    => 'Workshop',
                        'tipe_alat'     => 'Workshop',
                        'serial_number' => 'WS'
                    ]
                );

                $alatProyek = AlatProyek::firstOrCreate (
                    [ 
                        'id_proyek'           => $request->id_proyek,
                        'id_master_data_alat' => $masterDataAlat->id
                    ]
                );
            }
            else
            {
                // For regular equipment, find existing or create new
                $alatProyek = AlatProyek::where ( 'id_proyek', $request->id_proyek )
                    ->where ( 'id_master_data_alat', $request->id_master_data_alat )
                    ->first ();

                if ( ! $alatProyek )
                {
                    $alatProyek = AlatProyek::create ( [ 
                        'id_proyek'           => $request->id_proyek,
                        'id_master_data_alat' => $request->id_master_data_alat
                    ] );
                }
            }

            // Create the APB record
            $apb = APB::create ( [ 
                'tanggal'                  => $request->tanggal,
                'tipe'                     => $request->tipe,
                'mekanik'                  => $request->mekanik,
                'quantity'                 => $request->quantity,
                'id_saldo'                 => $saldo->id,
                'id_proyek'                => $request->id_proyek,
                'id_master_data_sparepart' => $masterDataSparepart->id,
                'id_master_data_supplier'  => $saldo->id_master_data_supplier,
                'id_alat_proyek'           => $alatProyek->id
            ] );

            // Decrement the saldo quantity
            $saldo->decrementQuantity ( $request->quantity );

            DB::commit ();
            return redirect ()->back ()->with ( 'success', 'Data APB berhasil ditambahkan (bypass).' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal menambahkan data APB (bypass): ' . $e->getMessage () );
        }
    }

    public function mutasi_store ( Request $request )
    {
        $validated = $request->validate ( [ 
            'tanggal'          => [ 'required', 'date' ],
            'id_proyek'        => [ 'required', 'exists:proyek,id' ],
            'id_proyek_tujuan' => [ 'required', 'exists:proyek,id', 'different:id_proyek' ],
            'id_saldo'         => [ 'required', 'exists:saldo,id' ],
            'quantity'         => [ 'required', 'integer', 'min:1' ],
            'tipe'             => [ 'required', 'string' ],
            'keterangan'       => [ 'nullable', 'string' ]
        ] );

        try
        {
            DB::beginTransaction ();
            $saldo             = Saldo::findOrFail ( $request->id_saldo );
            $pendingQuantity   = APB::where ( 'id_saldo', $saldo->id )->where ( 'tipe', 'mutasi-proyek' )->where ( 'status', 'pending' )->sum ( 'quantity' );
            $availableQuantity = $saldo->quantity - $pendingQuantity;
            if ( $availableQuantity < $request->quantity )
            {
                throw new \Exception( 'Stok sparepart tidak mencukupi. Sisa stok yang tersedia: ' . $availableQuantity . ' ' . $saldo->masterDataSparepart->satuan );
            }

            $newAPB = APB::create ( [ 
                'tanggal'                  => $request->tanggal,
                'tipe'                     => $request->tipe,
                'quantity'                 => $request->quantity,
                'status'                   => 'pending',
                'id_saldo'                 => $saldo->id,
                'id_proyek'                => $request->id_proyek,
                'id_tujuan_proyek'         => $request->id_proyek_tujuan,
                'id_master_data_sparepart' => $saldo->id_master_data_sparepart,
                'id_master_data_supplier'  => $saldo->id_master_data_supplier,
                'keterangan'               => $request->keterangan
            ] );

            ATB::create ( [ 
                'tanggal'                  => null,
                'tipe'                     => 'mutasi-proyek',
                'quantity'                 => null,
                'harga'                    => $saldo->harga,
                'id_proyek'                => $request->id_proyek_tujuan,
                'id_asal_proyek'           => $request->id_proyek,
                'id_apb_mutasi'            => $newAPB->id,
                'id_spb'                   => null,
                'id_detail_spb'            => null,
                'id_master_data_sparepart' => $saldo->id_master_data_sparepart,
                'id_master_data_supplier'  => $saldo->id_master_data_supplier
            ] );

            DB::commit ();
            return redirect ()->back ()->with ( 'success', 'Mutasi sparepart berhasil dibuat dan menunggu persetujuan.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal melakukan mutasi: ' . $e->getMessage () );
        }
    }

    public function mutasi_destroy ( $id )
    {
        try
        {
            DB::beginTransaction ();
            $apb = APB::findOrFail ( $id );
            if ( $apb->tipe !== 'mutasi-proyek' )
            {
                $apb->saldo->incrementQuantity ( $apb->quantity );
            }
            $apb->atbMutasi->delete ();
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
    public function destroy ( $id )
    {
        try
        {
            DB::beginTransaction ();
            $apb = APB::findOrFail ( $id );
            if ( $apb->tipe !== 'mutasi-proyek' )
            {
                $apb->saldo->incrementQuantity ( $apb->quantity );
            }
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

    private function applyBaseJoins ( $query )
    {
        $currentTipe = request ()->route ()->getName () === 'apb.mutasi_proyek' ? 'mutasi-proyek' : null;

        if ( $currentTipe === 'mutasi-proyek' )
        {
            return $query->leftJoin ( 'alat_proyek', 'apb.id_alat_proyek', '=', 'alat_proyek.id' )
                ->leftJoin ( 'master_data_alat', 'alat_proyek.id_master_data_alat', '=', 'master_data_alat.id' )
                ->join ( 'master_data_sparepart', 'apb.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
                ->join ( 'kategori_sparepart', 'master_data_sparepart.id_kategori_sparepart', '=', 'kategori_sparepart.id' )
                ->leftJoin ( 'master_data_supplier', 'apb.id_master_data_supplier', '=', 'master_data_supplier.id' )
                ->leftJoin ( 'saldo', 'apb.id_saldo', '=', 'saldo.id' )
                ->leftJoin ( 'proyek as tujuan_proyek', 'apb.id_tujuan_proyek', '=', 'tujuan_proyek.id' );
        }

        return $query->join ( 'alat_proyek', 'apb.id_alat_proyek', '=', 'alat_proyek.id' )
            ->join ( 'master_data_alat', 'alat_proyek.id_master_data_alat', '=', 'master_data_alat.id' )
            ->join ( 'master_data_sparepart', 'apb.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->join ( 'kategori_sparepart', 'master_data_sparepart.id_kategori_sparepart', '=', 'kategori_sparepart.id' )
            ->leftJoin ( 'master_data_supplier', 'apb.id_master_data_supplier', '=', 'master_data_supplier.id' )
            ->leftJoin ( 'saldo', 'apb.id_saldo', '=', 'saldo.id' )
            ->leftJoin ( 'proyek as tujuan_proyek', 'apb.id_tujuan_proyek', '=', 'tujuan_proyek.id' );
    }
}
