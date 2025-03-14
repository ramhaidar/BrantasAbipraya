<?php

namespace App\Http\Controllers;

use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SaldoController extends Controller
{
    // Helper function to decode selected values
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

    public function hutang_unit_alat ( Request $request )
    {
        return $this->showSaldoPage (
            "Hutang Unit Alat",
            "Data Saldo EX Unit Alat",
            $request->id_proyek
        );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showSaldoPage (
            "Panjar Unit Alat",
            "Data Saldo EX Panjar Unit Alat",
            $request->id_proyek
        );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showSaldoPage (
            "Mutasi Proyek",
            "Data Saldo EX Mutasi Proyek",
            $request->id_proyek
        );
    }

    public function panjar_proyek ( Request $request )
    {
        return $this->showSaldoPage (
            "Panjar Proyek",
            "Data Saldo EX Panjar Proyek",
            $request->id_proyek
        );
    }

    private function applyBaseJoins ( $query )
    {
        return $query->join ( 'atb', 'saldo.id_atb', '=', 'atb.id' )
            ->join ( 'master_data_sparepart', 'saldo.id_master_data_sparepart', '=', 'master_data_sparepart.id' )
            ->join ( 'kategori_sparepart', 'master_data_sparepart.id_kategori_sparepart', '=', 'kategori_sparepart.id' )
            ->leftJoin ( 'master_data_supplier', 'saldo.id_master_data_supplier', '=', 'master_data_supplier.id' );
    }

    /**
     * Extract unique values for all filterable fields
     * 
     * @param int $id_proyek Project ID
     * @param string $tipe Saldo type
     * @return array Associative array of unique values by field
     */
    private function getUniqueValues ( $id_proyek, $tipe )
    {
        // Create a new base query for all Saldo records of this type for this project
        $baseQuery = Saldo::where ( 'id_proyek', $id_proyek )
            ->where ( 'tipe', $tipe );

        // Get all Saldo IDs from the query to use in subqueries for better performance
        $saldoIds = $baseQuery->pluck ( 'id' )->toArray ();
        if ( empty ( $saldoIds ) )
        {
            return [ 
                'tanggal'      => [],
                'kode'         => [],
                'supplier'     => [],
                'sparepart'    => [],
                'merk'         => [],
                'part_number'  => [],
                'satuan'       => [],
                'quantity'     => [],
                'harga'        => [],
                'jumlah_harga' => [],
            ];
        }

        // Get unique dates in formatted form from ATB records linked to these Saldo records
        $dates = Saldo::whereIn ( 'id', $saldoIds )
            ->whereHas ( 'atb' )
            ->with ( 'atb' )
            ->get ()
            ->map ( function ($saldo)
            {
                return $saldo->atb ? date ( 'Y-m-d', strtotime ( $saldo->atb->tanggal ) ) : null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique kategori sparepart combinations (kode: nama)
        $kategoriSpareparts = Saldo::whereIn ( 'id', $saldoIds )
            ->whereHas ( 'masterDataSparepart.kategoriSparepart' )
            ->with ( 'masterDataSparepart.kategoriSparepart' )
            ->get ()
            ->map ( function ($saldo)
            {
                if ( $saldo->masterDataSparepart && $saldo->masterDataSparepart->kategoriSparepart )
                {
                    $kat = $saldo->masterDataSparepart->kategoriSparepart;
                    return $kat->kode . ': ' . $kat->nama;
                }
                return null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique suppliers
        $suppliers = Saldo::whereIn ( 'id', $saldoIds )
            ->whereHas ( 'masterDataSupplier' )
            ->with ( 'masterDataSupplier' )
            ->get ()
            ->map ( function ($saldo)
            {
                return $saldo->masterDataSupplier->nama ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique spareparts
        $spareparts = Saldo::whereIn ( 'id', $saldoIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($saldo)
            {
                return $saldo->masterDataSparepart->nama ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique merks
        $merks = Saldo::whereIn ( 'id', $saldoIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($saldo)
            {
                return $saldo->masterDataSparepart->merk ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique part numbers
        $partNumbers = Saldo::whereIn ( 'id', $saldoIds )
            ->whereHas ( 'masterDataSparepart' )
            ->with ( 'masterDataSparepart' )
            ->get ()
            ->map ( function ($saldo)
            {
                return $saldo->masterDataSparepart->part_number ?? null;
            } )
            ->filter ()
            ->unique ()
            ->values ()
            ->toArray ();

        // Get unique satuan values
        $satuanValues = Saldo::whereIn ( 'id', $saldoIds )
            ->whereNotNull ( 'satuan' )
            ->pluck ( 'satuan' )
            ->unique ()
            ->values ()
            ->toArray ();
        sort ( $satuanValues );

        // Get unique quantities
        $quantities = Saldo::whereIn ( 'id', $saldoIds )
            ->whereNotNull ( 'quantity' )
            ->pluck ( 'quantity' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Get unique harga values
        $hargaValues = Saldo::whereIn ( 'id', $saldoIds )
            ->whereNotNull ( 'harga' )
            ->pluck ( 'harga' )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Calculate jumlah_harga for uniqueness
        $jumlahHargaValues = Saldo::whereIn ( 'id', $saldoIds )
            ->whereNotNull ( 'quantity' )
            ->whereNotNull ( 'harga' )
            ->get ()
            ->map ( function ($saldo)
            {
                return $saldo->quantity * $saldo->harga;
            } )
            ->unique ()
            ->sort ()
            ->values ()
            ->toArray ();

        // Return all unique values
        return [ 
            'tanggal'      => $dates,
            'kode'         => $kategoriSpareparts,
            'supplier'     => $suppliers,
            'sparepart'    => $spareparts,
            'merk'         => $merks,
            'part_number'  => $partNumbers,
            'satuan'       => $satuanValues,
            'quantity'     => $quantities,
            'harga'        => $hargaValues,
            'jumlah_harga' => $jumlahHargaValues,
        ];
    }

    private function applyFilters ( $query, $request )
    {
        if (
            ! $request->hasAny ( [ 
                'selected_tanggal',
                'selected_kode',
                'selected_supplier',
                'selected_sparepart',
                'selected_merk',
                'selected_part_number',
                'selected_satuan',
                'selected_quantity',
                'selected_harga',
                'selected_jumlah_harga'
            ] )
        )
        {
            return $query;
        }

        // Check if joins already exist
        if ( ! $query->getQuery ()->joins )
        {
            $query = $this->applyBaseJoins ( $query );
        }

        $filters = [ 
            'tanggal'      => function ($q, $values)
            {
                $q->whereHas ( 'atb', function ($subQ) use ($values)
                {
                    // Start with a new query scope
                    $subQ->where ( function ($dateQ) use ($values)
                    {
                        $hasRange = false;
                        $gtDate = null;
                        $ltDate = null;

                        foreach ( $values as $value )
                        {
                            if ( $value === 'Empty/Null' || $value === 'null' )
                            {
                                $dateQ->orWhereNull ( 'tanggal' );
                            }
                            elseif ( strpos ( $value, 'exact:' ) === 0 )
                            {
                                $date = substr ( $value, 6 );
                                // Use proper date comparison
                                $dateQ->orWhere ( function ($exactQ) use ($date)
                                {
                                    $exactQ->whereRaw ( 'DATE(tanggal) = ?', [ $date ] );
                                } );
                            }
                            elseif ( strpos ( $value, 'gt:' ) === 0 )
                            {
                                $gtDate   = substr ( $value, 3 );
                                $hasRange = true;
                            }
                            elseif ( strpos ( $value, 'lt:' ) === 0 )
                            {
                                $ltDate   = substr ( $value, 3 );
                                $hasRange = true;
                            }
                        }

                        // Handle date range if present
                        if ( $hasRange )
                        {
                            $dateQ->orWhere ( function ($rangeQ) use ($gtDate, $ltDate)
                            {
                                if ( $gtDate )
                                {
                                    $rangeQ->whereRaw ( 'DATE(tanggal) >= ?', [ $gtDate ] );
                                }
                                if ( $ltDate )
                                {
                                    $rangeQ->whereRaw ( 'DATE(tanggal) <= ?', [ $ltDate ] );
                                }
                            } );
                        }
                    } );
                } );
            },
            'kode'         => function ($q, $values)
            {
                $q->where ( function ($q) use ($values)
                {
                    if ( in_array ( 'Empty/Null', $values ) )
                    {
                        $q->whereDoesntHave ( 'masterDataSparepart.kategoriSparepart' )
                            ->orWhereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq)
                            {
                                $sq->whereNull ( 'kode' )
                                    ->orWhere ( 'kode', '' )
                                    ->orWhere ( 'kode', '-' );
                            } );

                        $otherValues = array_diff ( $values, [ 'Empty/Null' ] );
                        if ( ! empty ( $otherValues ) )
                        {
                            $q->orWhereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq) use ($otherValues)
                            {
                                $sq->whereIn ( \DB::raw ( "CONCAT(kode, ': ', nama)" ), $otherValues );
                            } );
                        }
                    }
                    else
                    {
                        $q->whereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq) use ($values)
                        {
                            $sq->whereIn ( \DB::raw ( "CONCAT(kode, ': ', nama)" ), $values );
                        } );
                    }
                } );
            },
            'supplier'     => 'master_data_supplier.nama',
            'sparepart'    => 'master_data_sparepart.nama',
            'merk'         => 'master_data_sparepart.merk',
            'part_number'  => 'master_data_sparepart.part_number',
            'satuan'       => 'saldo.satuan',
            'quantity'     => function ($q, $values)
            {
                $q->where ( function ($subQ) use ($values)
                {
                    $hasRange    = false;
                    $gtValue     = null;
                    $ltValue     = null;
                    $exactValues = [];

                    foreach ( $values as $value )
                    {
                        if ( $value === 'Empty/Null' )
                        {
                            $subQ->orWhereNull ( 'saldo.quantity' )
                                ->orWhere ( 'saldo.quantity', 0 );
                        }
                        elseif ( strpos ( $value, 'exact:' ) === 0 )
                        {
                            $exactValue = substr ( $value, 6 );
                            $subQ->orWhere ( 'saldo.quantity', '=', $exactValue );
                        }
                        elseif ( strpos ( $value, 'gt:' ) === 0 )
                        {
                            $gtValue  = substr ( $value, 3 );
                            $hasRange = true;
                        }
                        elseif ( strpos ( $value, 'lt:' ) === 0 )
                        {
                            $ltValue  = substr ( $value, 3 );
                            $hasRange = true;
                        }
                        elseif ( is_numeric ( $value ) ) // Handle checkbox values
                        {
                            $exactValues[] = $value;
                        }
                    }

                    // Apply checkbox values if any exist
                    if ( ! empty ( $exactValues ) )
                    {
                        $subQ->orWhereIn ( 'saldo.quantity', $exactValues );
                    }

                    // Apply range if exists
                    if ( $hasRange )
                    {
                        $subQ->orWhere ( function ($rangeQ) use ($gtValue, $ltValue)
                        {
                            if ( $gtValue )
                            {
                                $rangeQ->where ( 'saldo.quantity', '>=', $gtValue );
                            }
                            if ( $ltValue )
                            {
                                $rangeQ->where ( 'saldo.quantity', '<=', $ltValue );
                            }
                        } );
                    }
                } );
            },
            'harga'        => function ($q, $values)
            {
                $q->where ( function ($subQ) use ($values)
                {
                    $hasRange    = false;
                    $gtValue     = null;
                    $ltValue     = null;
                    $exactValues = [];

                    foreach ( $values as $value )
                    {
                        if ( $value === 'Empty/Null' )
                        {
                            $subQ->orWhereNull ( 'saldo.harga' )
                                ->orWhere ( 'saldo.harga', 0 );
                        }
                        elseif ( strpos ( $value, 'exact:' ) === 0 )
                        {
                            $exactValue = substr ( $value, 6 );
                            $subQ->orWhere ( 'saldo.harga', '=', $exactValue );
                        }
                        elseif ( strpos ( $value, 'gt:' ) === 0 )
                        {
                            $gtValue  = substr ( $value, 3 );
                            $hasRange = true;
                        }
                        elseif ( strpos ( $value, 'lt:' ) === 0 )
                        {
                            $ltValue  = substr ( $value, 3 );
                            $hasRange = true;
                        }
                        elseif ( is_numeric ( $value ) ) // Handle checkbox values
                        {
                            $exactValues[] = $value;
                        }
                    }

                    // Apply checkbox values if any exist
                    if ( ! empty ( $exactValues ) )
                    {
                        $subQ->orWhereIn ( 'saldo.harga', $exactValues );
                    }

                    // Apply range if exists
                    if ( $hasRange )
                    {
                        $subQ->orWhere ( function ($rangeQ) use ($gtValue, $ltValue)
                        {
                            if ( $gtValue )
                            {
                                $rangeQ->where ( 'saldo.harga', '>=', $gtValue );
                            }
                            if ( $ltValue )
                            {
                                $rangeQ->where ( 'saldo.harga', '<=', $ltValue );
                            }
                        } );
                    }
                } );
            },
            'jumlah_harga' => function ($q, $values)
            {
                $q->where ( function ($subQ) use ($values)
                {
                    $hasRange    = false;
                    $gtValue     = null;
                    $ltValue     = null;
                    $exactValues = [];

                    foreach ( $values as $value )
                    {
                        if ( $value === 'Empty/Null' )
                        {
                            $subQ->orWhereRaw ( '(saldo.quantity * saldo.harga) IS NULL' )
                                ->orWhereRaw ( '(saldo.quantity * saldo.harga) = 0' );
                        }
                        elseif ( strpos ( $value, 'exact:' ) === 0 )
                        {
                            $exactValue = substr ( $value, 6 );
                            $subQ->orWhereRaw ( '(saldo.quantity * saldo.harga) = ?', [ $exactValue ] );
                        }
                        elseif ( strpos ( $value, 'gt:' ) === 0 )
                        {
                            $gtValue  = substr ( $value, 3 );
                            $hasRange = true;
                        }
                        elseif ( strpos ( $value, 'lt:' ) === 0 )
                        {
                            $ltValue  = substr ( $value, 3 );
                            $hasRange = true;
                        }
                        elseif ( is_numeric ( $value ) ) // Handle checkbox values
                        {
                            $exactValues[] = $value;
                        }
                    }

                    // Apply checkbox values if any exist
                    if ( ! empty ( $exactValues ) )
                    {
                        $subQ->orWhere ( function ($checkboxQ) use ($exactValues)
                        {
                            foreach ( $exactValues as $value )
                            {
                                $checkboxQ->orWhereRaw ( '(saldo.quantity * saldo.harga) = ?', [ $value ] );
                            }
                        } );
                    }

                    // Apply range if exists
                    if ( $hasRange )
                    {
                        $subQ->orWhere ( function ($rangeQ) use ($gtValue, $ltValue)
                        {
                            if ( $gtValue )
                            {
                                $rangeQ->whereRaw ( '(saldo.quantity * saldo.harga) >= ?', [ $gtValue ] );
                            }
                            if ( $ltValue )
                            {
                                $rangeQ->whereRaw ( '(saldo.quantity * saldo.harga) <= ?', [ $ltValue ] );
                            }
                        } );
                    }
                } );
            },
        ];

        foreach ( $filters as $param => $filter )
        {
            if ( $request->filled ( "selected_$param" ) )
            {
                $selectedValues = $this->getSelectedValues ( $request->get ( "selected_$param" ) );

                if ( is_callable ( $filter ) )
                {
                    // For tanggal which has custom filter logic
                    $query->where ( function ($q) use ($filter, $selectedValues)
                    {
                        $filter ( $q, $selectedValues );
                    } );
                }
                else
                {
                    // For other fields
                    if ( in_array ( 'null', $selectedValues ) )
                    {
                        $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                        $query->where ( function ($q) use ($filter, $nonNullValues)
                        {
                            $q->whereNull ( $filter )
                                ->orWhere ( $filter, '-' )
                                ->when ( ! empty ( $nonNullValues ), function ($q) use ($filter, $nonNullValues)
                                {
                                    $q->orWhereIn ( $filter, $nonNullValues );
                                } );
                        } );
                    }
                    else
                    {
                        $query->whereIn ( $filter, $selectedValues );
                    }
                }
            }
        }

        // Make sure to select only saldo fields to avoid duplicate columns
        return $query->select ( 'saldo.*' );
    }

    private function showSaldoPage ( $tipe, $pageTitle, $id_proyek )
    {
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) request ()->get ( 'per_page' ), $allowedPerPage ) ? (int) request ()->get ( 'per_page' ) : 10;

        // Clean and format tipe
        $tipe = strtolower ( str_replace ( ' ', '-', $tipe ) );

        // Get search query
        $search = request ()->get ( 'search', '' );

        // Get base Saldo query with relationships
        $query = Saldo::with ( [ 
            'atb',
            'masterDataSparepart.kategoriSparepart',
            'masterDataSupplier',
            'spb',
            'proyek',
            'asalProyek'
        ] )
            ->where ( 'saldo.id_proyek', $id_proyek )  // Add table prefix
            ->where ( 'saldo.tipe', $tipe );           // Add table prefix

        // Enhanced search functionality
        if ( $search )
        {
            console ( date ( '### Y-m-d H:i:s' ) . ": " . $search );

            $query->where ( function ($q) use ($search)
            {
                $searchLower = strtolower ( trim ( $search ) );
                $searchParts = explode ( ' ', $searchLower );

                // Array of Indonesian day names with their database equivalents
                $hariIndonesia = [ 
                    'senin'  => 'Monday',
                    'selasa' => 'Tuesday',
                    'rabu'   => 'Wednesday',
                    'kamis'  => 'Thursday',
                    'jumat'  => 'Friday',
                    "jum'at" => 'Friday',
                    'sabtu'  => 'Saturday',
                    'minggu' => 'Sunday',
                ];

                // Array of Indonesian month names with their numbers
                $bulanIndonesia = [ 
                    'januari'   => '01',
                    'februari'  => '02',
                    'maret'     => '03',
                    'april'     => '04',
                    'mei'       => '05',
                    'juni'      => '06',
                    'juli'      => '07',
                    'agustus'   => '08',
                    'september' => '09',
                    'oktober'   => '10',
                    'november'  => '11',
                    'desember'  => '12',
                ];

                $isDateSearch = false;
                $year         = null;
                $month        = null;
                $day          = null;

                // Check each part of the search string
                foreach ( $searchParts as $part )
                {
                    // Check for year
                    if ( is_numeric ( $part ) && strlen ( $part ) === 4 )
                    {
                        $year         = $part;
                        $isDateSearch = true;
                        continue;
                    }

                    // Check for day name
                    foreach ( $hariIndonesia as $indo => $eng )
                    {
                        if ( str_starts_with ( $indo, $part ) )
                        {
                            $isDateSearch = true;
                            $q->orWhereHas ( 'atb', function ($query) use ($eng)
                            {
                                // Ganti DAYNAME dengan TO_CHAR untuk PostgreSQL
                                $query->whereRaw ( "TO_CHAR(tanggal, 'Day') ILIKE ?", [ $eng . '%' ] );
                            } );
                            break 2; // Exit both loops if day is found
                        }
                    }

                    // Check for month name
                    foreach ( $bulanIndonesia as $indo => $num )
                    {
                        if ( str_starts_with ( $indo, $part ) )
                        {
                            $month        = $num;
                            $isDateSearch = true;
                            break;
                        }
                    }

                    // Check for day number only if it's not a numeric search
                    if ( is_numeric ( $part ) && strlen ( $part ) <= 2 && ! is_numeric ( str_replace ( [ ',', '.' ], '', $search ) ) )
                    {
                        $day          = sprintf ( "%02d", $part );
                        $isDateSearch = true;
                    }
                }

                // Apply date filters based on found components
                if ( $isDateSearch )
                {
                    $q->whereHas ( 'atb', function ($query) use ($year, $month, $day)
                    {
                        if ( $year )
                        {
                            $query->whereYear ( 'tanggal', $year );
                        }
                        if ( $month )
                        {
                            $query->whereMonth ( 'tanggal', $month );
                        }
                        if ( $day )
                        {
                            $query->whereDay ( 'tanggal', $day );
                        }
                    } );
                }
                else
                {
                    // For numeric searches - check first if it's a numeric search
                    if ( is_numeric ( str_replace ( [ ',', '.' ], '', $search ) ) )
                    {
                        $numericSearch = (float) str_replace ( [ ',', '.' ], '', $search );
                        $tolerance     = 0.1; // 10% tolerance
                        $min           = $numericSearch * ( 1 - $tolerance );
                        $max           = $numericSearch * ( 1 + $tolerance );

                        $q->orWhere ( function ($query) use ($numericSearch, $min, $max)
                        {
                            // First, try exact matches
                            $query->where ( function ($q) use ($numericSearch)
                            {
                                $q->where ( 'saldo.quantity', '=', $numericSearch )
                                    ->orWhere ( 'saldo.harga', '=', $numericSearch )
                                    ->orWhereRaw ( 'CAST((saldo.quantity * saldo.harga) AS DECIMAL(15,2)) = ?', [ $numericSearch ] );
                            } );

                            // Then try range matches
                            $query->orWhere ( function ($q) use ($min, $max)
                            {
                                $q->whereBetween ( 'saldo.quantity', [ $min, $max ] )
                                    ->orWhereBetween ( 'saldo.harga', [ $min, $max ] )
                                    ->orWhereRaw ( 'CAST((saldo.quantity * saldo.harga) AS DECIMAL(15,2)) BETWEEN ? AND ?', [ $min, $max ] );
                            } );
                        } );
                    }
                    else
                    {
                        // Text search for non-numeric values
                        $q->where ( function ($query) use ($search)
                        {
                            $query->whereHas ( 'spb', function ($q) use ($search)
                            {
                                $q->where ( 'nomor', 'ilike', "%{$search}%" );
                            } )
                                ->orWhereHas ( 'masterDataSparepart', function ($q) use ($search)
                                {
                                    $q->where ( 'nama', 'ilike', "%{$search}%" )
                                        ->orWhere ( 'part_number', 'ilike', "%{$search}%" )
                                        ->orWhere ( 'merk', 'ilike', "%{$search}%" )
                                        ->orWhereHas ( 'kategoriSparepart', function ($q) use ($search)
                                        {
                                            $q->where ( 'kode', 'ilike', "%{$search}%" )
                                                ->orWhere ( 'nama', 'ilike', "%{$search}%" );
                                        } );
                                } )
                                ->orWhereHas ( 'masterDataSupplier', function ($q) use ($search)
                                {
                                    $q->where ( 'nama', 'ilike', "%{$search}%" );
                                } )
                                ->orWhereHas ( 'asalProyek', function ($q) use ($search)
                                {
                                    $q->where ( 'nama', 'ilike', "%{$search}%" );
                                } )
                                ->orWhere ( 'saldo.satuan', 'ilike', "%{$search}%" )
                                ->orWhere ( 'saldo.quantity', '=', $search );
                        } );
                    }
                }
            } );
        }

        // Apply filters to query if any filter is active
        $query = $this->applyFilters ( $query, request () );

        // Get unique values for filters - changed to use project ID and type instead of query
        $uniqueValues = $this->getUniqueValues ( $id_proyek, $tipe );

        // Calculate total amount
        $totalAmount = $query->sum ( \DB::raw ( 'saldo.quantity * saldo.harga' ) );

        // Ensure we have required joins for final query
        if ( ! $query->getQuery ()->joins )
        {
            $query = $this->applyBaseJoins ( $query );
        }

        // Get paginated results
        $TableData = $query->select ( 'saldo.*' )
            ->orderBy ( 'atb.tanggal', 'desc' )
            ->orderBy ( 'saldo.updated_at', 'desc' )
            ->orderBy ( 'saldo.id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Add total amount to pagination object
        $TableData->total_amount = $totalAmount;

        $proyek = Proyek::with ( "users" )->findOrFail ( $id_proyek );

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

        return view ( "dashboard.saldo.saldo", [ 
            "proyek"         => $proyek,
            "proyeks"        => $proyeks,
            "TableData"      => $TableData, // Changed from saldos to TableData
            "headerPage"     => $proyek->nama,
            "page"           => $pageTitle,
            "search"         => $search,
            "tipe"           => $tipe,
            "allowedPerPage" => $allowedPerPage, // Add this to view
            "perPage"        => $perPage, // Add this to view
            "uniqueValues"   => $uniqueValues, // Add this to view
        ] );
    }

    public function store ( $data )
    {
        try
        {
            $saldoData = [ 
                'tipe'                     => $data[ 'tipe' ],
                'quantity'                 => $data[ 'quantity' ],
                'harga'                    => $data[ 'harga' ],
                'id_proyek'                => $data[ 'id_proyek' ],
                'id_master_data_sparepart' => $data[ 'id_master_data_sparepart' ],
                'id_master_data_supplier'  => $data[ 'id_master_data_supplier' ]
            ];

            // Add fields based on type
            if ( $data[ 'tipe' ] === 'hutang-unit-alat' || $data[ 'tipe' ] === 'hutang-unit-alat-bypass' )
            {
                // For both normal and bypass types
                $saldoData[ 'satuan' ] = $data[ 'satuan' ];

                // Only add id_spb for normal type
                if ( $data[ 'tipe' ] === 'hutang-unit-alat' && isset ( $data[ 'id_spb' ] ) )
                {
                    $saldoData[ 'id_spb' ] = $data[ 'id_spb' ];
                }
            }
            else if ( $data[ 'tipe' ] === 'mutasi-proyek' )
            {
                $saldoData[ 'id_asal_proyek' ] = $data[ 'id_asal_proyek' ];
                $saldoData[ 'satuan' ]         = $data[ 'satuan' ];
            }
            else if ( $data[ 'tipe' ] === 'panjar-unit-alat' || $data[ 'tipe' ] === 'panjar-proyek' )
            {
                $saldoData[ 'satuan' ] = $data[ 'satuan' ];
            }

            // Add id_atb for all types
            if ( isset ( $data[ 'id_atb' ] ) )
            {
                $saldoData[ 'id_atb' ] = $data[ 'id_atb' ];
            }

            Saldo::create ( $saldoData );
            return true;
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }

    public function destroy ( $id )
    {
        try
        {
            $saldo = Saldo::findOrFail ( $id );
            $saldo->delete ();
            return true;
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }
}
