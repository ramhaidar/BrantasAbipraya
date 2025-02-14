<?php
namespace App\Http\Controllers;

use App\Models\ATB;
use App\Models\RKB;
use App\Models\SPB;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\DetailSPB;
use Illuminate\Http\Request;
use App\Models\MasterDataSupplier;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SaldoController;
use App\Models\KategoriSparepart; // Add this line

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
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) request ()->get ( 'per_page' ), $allowedPerPage ) ? (int) request ()->get ( 'per_page' ) : 10;

        // Clean and format tipe
        $tipe = strtolower ( str_replace ( ' ', '-', $tipe ) );

        // Get search query
        $search = request ()->get ( 'search', '' );

        // Get base ATB query with relationships
        $query = ATB::with ( [ 
            'spb',
            'masterDataSparepart.kategoriSparepart',
            'masterDataSupplier',
            'detailSpb',
            'apbMutasi',
            'asalProyek'
        ] )
            ->where ( 'id_proyek', $id_proyek )
            ->where ( 'tipe', $tipe );

        // Enhanced search functionality
        if ( $search )
        {
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
                            $q->orWhereRaw ( "TO_CHAR(tanggal, 'Day') ILIKE ?", [ $eng . '%' ] );
                            break 2;
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

                    // Check for day number
                    if ( is_numeric ( $part ) && strlen ( $part ) <= 2 )
                    {
                        $day          = sprintf ( "%02d", $part );
                        $isDateSearch = true;
                    }
                }

                // Apply date filters based on found components
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
                    // Existing non-date search criteria
                    $q->where ( function ($q) use ($search)
                    {
                        $q->whereHas ( 'spb', function ($q) use ($search)
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
                            ->orWhereHas ( 'detailSpb', function ($q) use ($search)
                            {
                                $q->where ( 'satuan', 'ilike', "%{$search}%" );
                            } )
                            ->orWhereHas ( 'asalProyek', function ($q) use ($search)
                            {
                                $q->where ( 'nama', 'ilike', "%{$search}%" );
                            } );

                        // For numeric searches
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
                                    $q->where ( 'atb.quantity', '=', $numericSearch )
                                        ->orWhere ( 'atb.harga', '=', $numericSearch )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga) AS DECIMAL(15,2)) = ?', [ $numericSearch ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga * 0.11) AS DECIMAL(15,2)) = ?', [ $numericSearch ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga * 1.11) AS DECIMAL(15,2)) = ?', [ $numericSearch ] );
                                } );

                                // Then try range matches
                                $query->orWhere ( function ($q) use ($min, $max)
                                {
                                    $q->whereBetween ( 'atb.quantity', [ $min, $max ] )
                                        ->orWhereBetween ( 'atb.harga', [ $min, $max ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga) AS DECIMAL(15,2)) BETWEEN ? AND ?', [ $min, $max ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga * 0.11) AS DECIMAL(15,2)) BETWEEN ? AND ?', [ $min, $max ] )
                                        ->orWhereRaw ( 'CAST((atb.quantity * atb.harga * 1.11) AS DECIMAL(15,2)) BETWEEN ? AND ?', [ $min, $max ] );
                                } );
                            } );
                        }
                    } );
                }
            } );
        }

        // Apply filters if any exist
        if ( request ()->has ( 'selected_nomor_spb' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_nomor_spb' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'spb', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'nomor', $nonNullValues );
                    } )->orWhereDoesntHave ( 'spb' );
                } );
            }
            else
            {
                $query->whereHas ( 'spb', function ($q) use ($selectedValues)
                {
                    $q->whereIn ( 'nomor', $selectedValues );
                } );
            }
        }

        if ( request ()->has ( 'selected_tanggal' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_tanggal' ) );
            $query->where ( function ($q) use ($selectedValues)
            {
                // Create a tracking array for range conditions
                $rangeConditions = [ 
                    'gt' => null,
                    'lt' => null
                ];

                // First pass - collect all conditions
                foreach ( $selectedValues as $value )
                {
                    if ( $value === 'Empty/Null' || $value === 'null' )
                    {
                        $q->orWhereNull ( 'tanggal' );
                    }
                    elseif ( strpos ( $value, 'exact:' ) === 0 )
                    {
                        $date = substr ( $value, 6 );
                        $q->orWhereRaw ( "DATE(atb.tanggal) = ?", [ $date ] );
                    }
                    elseif ( strpos ( $value, 'gt:' ) === 0 )
                    {
                        $rangeConditions[ 'gt' ] = substr ( $value, 3 );
                    }
                    elseif ( strpos ( $value, 'lt:' ) === 0 )
                    {
                        $rangeConditions[ 'lt' ] = substr ( $value, 3 );
                    }
                    elseif ( preg_match ( '/^(\d{4}-\d{2}-\d{2})\.\.(\d{4}-\d{2}-\d{2})$/', $value, $matches ) )
                    {
                        $q->orWhereBetween ( 'tanggal', [ $matches[ 1 ], $matches[ 2 ] ] );
                    }
                }

                // Handle date range as a single condition if either gt or lt exist
                if ( $rangeConditions[ 'gt' ] || $rangeConditions[ 'lt' ] )
                {
                    $q->orWhere ( function ($rangeQ) use ($rangeConditions)
                    {
                        if ( $rangeConditions[ 'gt' ] )
                        {
                            $rangeQ->whereRaw ( "DATE(atb.tanggal) >= ?", [ $rangeConditions[ 'gt' ] ] );
                        }
                        if ( $rangeConditions[ 'lt' ] )
                        {
                            $rangeQ->whereRaw ( "DATE(atb.tanggal) <= ?", [ $rangeConditions[ 'lt' ] ] );
                        }
                    } );
                }
            } );
        }

        if ( request ()->has ( 'selected_kode' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_kode' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'masterDataSparepart.kategoriSparepart', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'kode', $nonNullValues );
                    } )->orWhereDoesntHave ( 'masterDataSparepart.kategoriSparepart' );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataSparepart.kategoriSparepart', function ($q) use ($selectedValues)
                {
                    $q->whereIn ( 'kode', $selectedValues );
                } );
            }
        }

        if ( request ()->has ( 'selected_supplier' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_supplier' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'masterDataSupplier', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'nama', $nonNullValues );
                    } )->orWhereDoesntHave ( 'masterDataSupplier' );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataSupplier', function ($q) use ($selectedValues)
                {
                    $q->whereIn ( 'nama', $selectedValues );
                } );
            }
        }

        if ( request ()->has ( 'selected_sparepart' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_sparepart' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'nama', $nonNullValues );
                    } )->orWhereDoesntHave ( 'masterDataSparepart' );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataSparepart', function ($q) use ($selectedValues)
                {
                    $q->whereIn ( 'nama', $selectedValues );
                } );
            }
        }

        if ( request ()->has ( 'selected_merk' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_merk' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'merk', $nonNullValues );
                    } )->orWhereDoesntHave ( 'masterDataSparepart' );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataSparepart', function ($q) use ($selectedValues)
                {
                    $q->whereIn ( 'merk', $selectedValues );
                } );
            }
        }

        if ( request ()->has ( 'selected_part_number' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_part_number' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'masterDataSparepart', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'part_number', $nonNullValues );
                    } )->orWhereDoesntHave ( 'masterDataSparepart' );
                } );
            }
            else
            {
                $query->whereHas ( 'masterDataSparepart', function ($q) use ($selectedValues)
                {
                    $q->whereIn ( 'part_number', $selectedValues );
                } );
            }
        }

        if ( request ()->has ( 'selected_quantity' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_quantity' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereIn ( 'quantity', $nonNullValues )
                        ->orWhereNull ( 'quantity' );
                } );
            }
            else
            {
                $query->whereIn ( 'quantity', $selectedValues );
            }
        }

        if ( request ()->has ( 'selected_satuan' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_satuan' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'detailSpb', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'satuan', $nonNullValues );
                    } )->orWhereHas ( 'saldo', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'satuan', $nonNullValues );
                    } )->orWhereDoesntHave ( 'detailSpb' )
                        ->orWhereDoesntHave ( 'saldo' );
                } );
            }
            else
            {
                $query->where ( function ($q) use ($selectedValues)
                {
                    $q->whereHas ( 'detailSpb', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'satuan', $selectedValues );
                    } )->orWhereHas ( 'saldo', function ($sq) use ($selectedValues)
                    {
                        $sq->whereIn ( 'satuan', $selectedValues );
                    } );
                } );
            }
        }

        if ( request ()->has ( 'selected_harga' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_harga' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereIn ( 'harga', $nonNullValues )
                        ->orWhereNull ( 'harga' );
                } );
            }
            else
            {
                $query->whereIn ( 'harga', $selectedValues );
            }
        }

        if ( request ()->has ( 'selected_jumlah_harga' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_jumlah_harga' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereIn ( DB::raw ( '(quantity * harga)' ), $nonNullValues )
                        ->orWhereNull ( 'quantity' )
                        ->orWhereNull ( 'harga' );
                } );
            }
            else
            {
                $query->whereRaw ( '(quantity * harga) IN (?)', [ implode ( ',', $selectedValues ) ] );
            }
        }

        if ( request ()->has ( 'selected_ppn' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_ppn' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereIn ( DB::raw ( 'CAST((quantity * harga * 0.11) AS DECIMAL(15,2))' ), $nonNullValues )
                        ->orWhereNull ( 'quantity' )
                        ->orWhereNull ( 'harga' );
                } );
            }
            else
            {
                $query->whereRaw ( 'CAST((quantity * harga * 0.11) AS DECIMAL(15,2)) IN (?)', [ implode ( ',', $selectedValues ) ] );
            }
        }

        if ( request ()->has ( 'selected_bruto' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_bruto' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereIn ( DB::raw ( 'CAST((quantity * harga * 1.11) AS DECIMAL(15,2))' ), $nonNullValues )
                        ->orWhereNull ( 'quantity' )
                        ->orWhereNull ( 'harga' );
                } );
            }
            else
            {
                $query->whereRaw ( 'CAST((quantity * harga * 1.11) AS DECIMAL(15,2)) IN (?)', [ implode ( ',', $selectedValues ) ] );
            }
        }

        if ( request ()->has ( 'selected_asal_proyek' ) )
        {
            $selectedValues = $this->getSelectedValues ( request ( 'selected_asal_proyek' ) );
            if ( in_array ( 'null', $selectedValues ) )
            {
                $nonNullValues = array_filter ( $selectedValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'asalProyek', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'nama', $nonNullValues );
                    } )->orWhereDoesntHave ( 'asalProyek' );
                } );
            }
            else
            {
                $query->whereHas ( 'asalProyek', function ($q) use ($selectedValues)
                {
                    $q->whereIn ( 'nama', $selectedValues );
                } );
            }
        }

        // Get unique values for filters
        $uniqueValues = $this->getUniqueValues ( $query );

        // Calculate total amounts for all records before pagination
        $totalQuery = clone $query;
        $totals     = $totalQuery->selectRaw ( '
            SUM(quantity * harga) as total_harga,
            SUM(quantity * harga * 0.11) as total_ppn,
            SUM(quantity * harga * 1.11) as total_bruto
        ' )->first ();

        // Get paginated results
        $TableData = $query->orderBy ( 'tanggal', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Add totals to pagination object
        $TableData->total_harga = $totals->total_harga;
        $TableData->total_ppn   = $totals->total_ppn;
        $TableData->total_bruto = $totals->total_bruto;

        // Get required data
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

        // Get SPBs if needed
        $spbs = collect ();
        if ( $tipe === 'hutang-unit-alat' )
        {
            $rkbs = RKB::with ( "spbs.linkSpbDetailSpb.detailSpb" )
                ->where ( 'id_proyek', $id_proyek )
                ->get ();

            foreach ( $rkbs as $rkb )
            {
                $filteredSpbs = $rkb->spbs->filter ( function ($spb)
                {
                    $hasRemainingQuantity = $spb->linkSpbDetailSpb->some ( function ($link)
                    {
                        return $link->detailSpb->quantity_belum_diterima > 0;
                    } );

                    return $hasRemainingQuantity &&
                        ( ( ! $spb->is_addendum && ! isset ( $spb->id_spb_original ) ) ||
                            ( $spb->is_addendum && isset ( $spb->id_spb_original ) ) );
                } );

                $spbs = $spbs->merge ( $filteredSpbs );
            }
        }

        // Get additional data based on type
        $spareparts = null;
        if ( $tipe === 'panjar-unit-alat' || $tipe === 'panjar-proyek' )
        {
            $spareparts = MasterDataSparepart::with ( 'KategoriSparepart' )
                ->orderByDesc ( 'updated_at' )
                ->get ();
        }
        elseif ( $tipe === 'hutang-unit-alat' && in_array ( Auth::user ()->role, [ 'admin_divisi', 'vp', 'svp', 'superadmin' ] ) )
        {
            $spareparts = MasterDataSparepart::with ( 'KategoriSparepart' )
                ->orderByDesc ( 'updated_at' )
                ->get ();
        }

        // Get common data
        $kategoriSpareparts  = KategoriSparepart::all ();
        $masterDataSuppliers = MasterDataSupplier::all ();

        // Get selected values for filters
        $selectedValues = $this->getSelectedValues ( request ( 'selected_nomor_spb' ) );

        return view ( "dashboard.atb.atb", [ 
            "proyek"             => $proyek,
            "proyeks"            => $proyeks,
            "spbs"               => $spbs,
            "tipe"               => $tipe,
            "TableData"          => $TableData,
            "suppliers"          => $masterDataSuppliers,
            "spareparts"         => $spareparts,
            "kategoriSpareparts" => $kategoriSpareparts,
            "headerPage"         => $proyek->nama,
            "page"               => $pageTitle,
            "search"             => $search,
            "uniqueValues"       => $uniqueValues,
            "selectedValues"     => $selectedValues,
        ] );
    }

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

    private function getUniqueValues ( $query )
    {
        // Clone the query to avoid modifying the original
        $baseQuery = clone $query;

        // Remove existing selects to avoid conflicts
        $baseQuery->getQuery ()->selects = null;

        // Get unique values for SPB numbers
        $results = $baseQuery->with ( 'spb' )->get ();

        return [ 
            'nomor_spb'         => $results->pluck ( 'spb.nomor' )->filter ()->unique ()->values (),
            'tanggal'           => $results->pluck ( 'tanggal' )->filter ()->unique ()->values (),
            'kode'              => $results->map ( function ($item)
            {
                return $item->masterDataSparepart->kategoriSparepart->kode ?? null;
            } )->filter ()->unique ()->values (),
            'supplier'          => $results->map ( function ($item)
            {
                return $item->masterDataSupplier->nama ?? null;
            } )->filter ()->unique ()->values (),
            'sparepart'         => $results->map ( function ($item)
            {
                return $item->masterDataSparepart->nama ?? null;
            } )->filter ()->unique ()->values (),
            'merk'              => $results->map ( function ($item)
            {
                return $item->masterDataSparepart->merk ?? null;
            } )->filter ()->unique ()->values (),
            'part_number'       => $results->map ( function ($item)
            {
                return $item->masterDataSparepart->part_number ?? null;
            } )->filter ()->unique ()->values (),
            'quantity'          => $results->pluck ( 'quantity' )->filter ()->unique ()->sort ()->values (),
            'satuan'            => $results->map ( function ($item)
            {
                return $item->detailSpb->satuan ?? ( $item->saldo->satuan ?? null );
            } )->filter ()->unique ()->values (),
            'harga'             => $results->pluck ( 'harga' )->filter ()->unique ()->sort ()->values (),
            'jumlah_harga'      => $results->map ( function ($item)
            {
                return $item->quantity * $item->harga;
            } )->filter ()->unique ()->sort ()->values (),
            'ppn'               => $results->map ( function ($item)
            {
                return $item->quantity * $item->harga * 0.11;
            } )->filter ()->unique ()->sort ()->values (),
            'bruto'             => $results->map ( function ($item)
            {
                return $item->quantity * $item->harga * 1.11;
            } )->filter ()->unique ()->sort ()->values (),
            'asal_proyek'       => $results->map ( function ($item)
            {
                return $item->asalProyek->nama ?? null;
            } )->filter ()->unique ()->values (),
            'quantity_dikirim'  => $results->map ( function ($item)
            {
                return $item->apbMutasi->quantity ?? null;
            } )->filter ()->unique ()->sort ()->values (),
            'quantity_diterima' => $results->map ( function ($item)
            {
                return $item->quantity;
            } )->filter ()->unique ()->sort ()->values (),
        ];
    }

    public function getlinkSpbDetailSpbs ( $id )
    {
        $SPB = SPB::with ( [ 
            "linkSpbDetailSpb.detailSpb.MasterDataSparepart",
            "linkSpbDetailSpb.detailSpb.linkSpbDetailSpb.spb",
        ] )->find ( $id );

        $DetailSPB = [];

        foreach ( $SPB->linkSpbDetailSpb as $item )
        {
            $DetailSPB[] = $item->detailSpb;
        }

        $html = view ( 'dashboard.atb.partials.spb-details-table', [ 'spbDetails' => $DetailSPB ] )->render ();

        return response ()->json ( [ 'html' => $html ] );
    }

    public function store ( Request $request )
    {
        // dd ( $request->all () );
        try
        {
            DB::beginTransaction ();

            if ( $request->tipe === "hutang-unit-alat-bypass" )
            {
                // Validate request for bypass type
                $validated = $request->validate ( [ 
                    'tipe'                     => 'required|string',
                    'tanggal'                  => 'required|date',
                    'id_proyek'                => 'required|exists:proyek,id',
                    'id_master_data_supplier'  => 'required|exists:master_data_supplier,id',
                    'id_master_data_sparepart' => 'required|exists:master_data_sparepart,id',
                    'quantity'                 => 'required|integer|min:1',
                    'harga'                    => 'required|numeric|min:0',
                    'satuan'                   => 'required|string'
                ] );

                // Create ATB record for bypass type
                $atb = ATB::create ( [ 
                    'tipe'                     => 'hutang-unit-alat',
                    'tanggal'                  => $request->tanggal,
                    'quantity'                 => $request->quantity,
                    'harga'                    => $request->harga,
                    'id_proyek'                => $request->id_proyek,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart,
                    'id_master_data_supplier'  => $request->id_master_data_supplier,
                    'satuan'                   => $request->satuan
                ] );

                // Create corresponding Saldo record
                $saldoController = new SaldoController();
                $saldoController->store ( [ 
                    'tipe'                     => 'hutang-unit-alat',
                    'quantity'                 => $request->quantity,
                    'harga'                    => $request->harga,
                    'id_proyek'                => $request->id_proyek,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart,
                    'id_master_data_supplier'  => $request->id_master_data_supplier,
                    'id_atb'                   => $atb->id,
                    'satuan'                   => $request->satuan
                ] );

            }
            elseif ( $request->tipe === "hutang-unit-alat" )
            {

                // Update validation rule for quantity
                $validated = $request->validate ( [ 
                    'tipe'                       => 'required|string',
                    'tanggal'                    => 'required|date',
                    'id_proyek'                  => 'required|exists:proyek,id',
                    'id_spb'                     => 'required|exists:spb,id',
                    'surat_tanda_terima'         => 'required|file|mimes:pdf|max:10240',
                    'quantity'                   => 'required|array',
                    'quantity.*'                 => 'required|integer|min:0', // Allow 0 quantity
                    'id_detail_spb'              => 'required|array',
                    'id_detail_spb.*'            => 'required|exists:detail_spb,id',
                    'id_master_data_sparepart'   => 'required|array',
                    'id_master_data_sparepart.*' => 'required|exists:master_data_sparepart,id',
                    'harga'                      => 'required|array',
                    'harga.*'                    => 'required|numeric|min:0',
                    'documentation_photos'       => 'array',
                    'documentation_photos.*'     => 'array',
                    'documentation_photos.*.*'   => 'file|image|mimes:jpeg,png,jpg|max:2048',
                    'id_master_data_supplier'    => 'required|array',
                    'id_master_data_supplier.*'  => 'required|exists:master_data_supplier,id',
                    'satuan'                     => 'required|array', // New validation rule
                    'satuan.*'                   => 'required|string' // New validation rule
                ] );

                // Create base storage paths
                $folderName      = 'atb_' . date ( 'YmdHis' ) . '_' . uniqid ();
                $baseStoragePath = 'uploads/atb/' . $folderName;
                $suratPath       = $baseStoragePath . '/surat';
                Storage::disk ( 'public' )->makeDirectory ( $suratPath );

                // Handle surat_tanda_terima upload
                $suratFile     = $request->file ( 'surat_tanda_terima' );
                $originalName  = pathinfo ( $suratFile->getClientOriginalName (), PATHINFO_FILENAME );
                $extension     = $suratFile->getClientOriginalExtension ();
                $timestamp     = now ()->format ( 'Y-m-d--H-i-s' );
                $suratFileName = "{$originalName}___{$timestamp}.{$extension}";
                $suratFilePath = $suratFile->storeAs ( $suratPath, $suratFileName, 'public' );

                $saldoController = new SaldoController();
                $processedItems  = 0;
                $skippedReasons  = [];

                // Process each detail SPB item
                foreach ( $request->id_detail_spb as $index => $id_detail_spb )
                {
                    // Get the DetailSPB record
                    $detailSpb         = DetailSPB::find ( $id_detail_spb );
                    $requestedQuantity = intval ( $request->quantity[ $index ] );

                    // Skip if quantity is invalid or item has no remaining quantity
                    if ( $requestedQuantity < 0 )
                    {
                        $skippedReasons[] = "Index $index: Invalid quantity";
                        continue;
                    }

                    // Skip validation for documentation photos if quantity is 0
                    if ( $requestedQuantity > 0 )
                    {
                        // Check for documentation photos with both current index and index+1
                        $hasPhotos = isset ( $request->file ( 'documentation_photos' )[ $index ] ) ||
                            isset ( $request->file ( 'documentation_photos' )[ $index + 1 ] );

                        if ( ! $hasPhotos )
                        {
                            $skippedReasons[] = "Index $index: Missing photos for non-zero quantity";
                            continue;
                        }
                    }

                    // Only continue processing if quantity is greater than 0
                    if ( $requestedQuantity > 0 )
                    {
                        // Validate remaining quantity
                        if ( $detailSpb->quantity_belum_diterima <= 0 )
                        {
                            $skippedReasons[] = "Index $index: No remaining quantity";
                            continue;
                        }

                        // Validate that we have all required data for this index
                        if (
                            ! isset ( $request->id_master_data_sparepart[ $index ] ) ||
                            ! isset ( $request->harga[ $index ] ) ||
                            ! isset ( $request->id_master_data_supplier[ $index ] )
                        )
                        {
                            if ( ! isset ( $request->id_master_data_sparepart[ $index ] ) )
                            {
                                $skippedReasons[] = "Index $index: Missing sparepart data";
                            }
                            if ( ! isset ( $request->harga[ $index ] ) )
                            {
                                $skippedReasons[] = "Index $index: Missing harga";
                            }
                            if ( ! isset ( $request->id_master_data_supplier[ $index ] ) )
                            {
                                $skippedReasons[] = "Index $index: Missing supplier";
                            }
                            if ( ! isset ( $request->file ( 'documentation_photos' )[ $index ] ) )
                            {
                                $skippedReasons[] = "Index $index: Missing photos";
                            }
                            continue;
                        }

                        // Create documentation folder for this item
                        $docPath = $baseStoragePath . '/dokumentasi_' . uniqid ();
                        Storage::disk ( 'public' )->makeDirectory ( $docPath );

                        // Try to get photos from either current index or index+1
                        $photos = $request->file ( 'documentation_photos' )[ $index ] ??
                            $request->file ( 'documentation_photos' )[ $index + 1 ] ??
                            null;

                        if ( $photos )
                        {
                            foreach ( $photos as $photo )
                            {
                                $photoName      = pathinfo ( $photo->getClientOriginalName (), PATHINFO_FILENAME );
                                $photoExt       = $photo->getClientOriginalExtension ();
                                $photoTimestamp = now ()->format ( 'Y-m-d--H-i-s' );
                                $fileName       = "{$photoName}___{$photoTimestamp}.{$photoExt}";
                                $photo->storeAs ( $docPath, $fileName, 'public' );
                            }
                        }

                        // Update quantity_belum_diterima
                        $detailSpb->reduceQuantityBelumDiterima ( $requestedQuantity );

                        // Create ATB record
                        $atb = ATB::create ( [ 
                            'tipe'                     => $request->tipe,
                            'dokumentasi_foto'         => $docPath,
                            'surat_tanda_terima'       => $suratFilePath,
                            'tanggal'                  => $request->tanggal,
                            'quantity'                 => $requestedQuantity,
                            'harga'                    => $request->harga[ $index ],
                            'id_proyek'                => $request->id_proyek,
                            'id_spb'                   => $request->id_spb,
                            'id_detail_spb'            => $id_detail_spb,
                            'id_master_data_sparepart' => $request->id_master_data_sparepart[ $index ],
                            'id_master_data_supplier'  => $request->id_master_data_supplier[ $index ],
                            'satuan'                   => $request->satuan[ $index ] // New column
                        ] );

                        // Create corresponding Saldo record
                        $saldoController->store ( [ 
                            'tipe'                     => $request->tipe,
                            'quantity'                 => $requestedQuantity,
                            'harga'                    => $request->harga[ $index ],
                            'id_proyek'                => $request->id_proyek,
                            'id_asal_proyek'           => null, // Add this line
                            'id_spb'                   => $request->id_spb,
                            'id_master_data_sparepart' => $request->id_master_data_sparepart[ $index ],
                            'id_master_data_supplier'  => $request->id_master_data_supplier[ $index ],
                            'id_atb'                   => $atb->id, // New column
                            'satuan'                   => $request->satuan[ $index ] // New column
                        ] );

                        $processedItems++;
                    }
                }

                if ( $processedItems === 0 )
                {
                    throw new \Exception( 'Tidak ada item yang valid untuk diproses. Alasan: ' . implode ( ', ', $skippedReasons ) );
                }

            }

            if ( $request->tipe == "panjar-proyek" || $request->tipe === "panjar-unit-alat" )
            {
                $validated = $request->validate ( [ 
                    'tipe'                     => 'required|string',
                    'tanggal'                  => 'required|date',
                    'id_proyek'                => 'required|exists:proyek,id',
                    'id_kategori_sparepart'    => 'required|exists:kategori_sparepart,id',
                    'id_master_data_sparepart' => 'required|exists:master_data_sparepart,id',
                    'id_master_data_supplier'  => 'required|exists:master_data_supplier,id', // Add this line
                    'quantity'                 => 'required|integer|min:1',
                    'harga'                    => 'required|numeric|min:0',
                    'satuan'                   => 'required|string', // Add this line
                    'dokumentasi'              => 'required|array',
                    'dokumentasi.*'            => 'file|image|mimes:jpeg,png,jpg|max:2048'
                ] );

                // Create base storage paths
                $folderName      = 'atb_' . date ( 'YmdHis' ) . '_' . uniqid ();
                $baseStoragePath = 'uploads/atb/' . $folderName;
                $docPath         = $baseStoragePath . '/dokumentasi';
                Storage::disk ( 'public' )->makeDirectory ( $docPath );

                // Handle dokumentasi upload
                $photos = $request->file ( 'dokumentasi' );
                foreach ( $photos as $photo )
                {
                    $photoName      = pathinfo ( $photo->getClientOriginalName (), PATHINFO_FILENAME );
                    $photoExt       = $photo->getClientOriginalExtension ();
                    $photoTimestamp = now ()->format ( 'Y-m-d--H-i-s' );
                    $fileName       = "{$photoName}___{$photoTimestamp}.{$photoExt}";
                    $photo->storeAs ( $docPath, $fileName, 'public' );
                }

                // Create ATB record
                $atb = ATB::create ( [ 
                    'tipe'                     => $request->tipe,
                    'dokumentasi_foto'         => $docPath,
                    'tanggal'                  => $request->tanggal,
                    'quantity'                 => $request->quantity,
                    'harga'                    => $request->harga,
                    'satuan'                   => $request->satuan, // Add this line
                    'id_proyek'                => $request->id_proyek,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart,
                    'id_master_data_supplier'  => $request->id_master_data_supplier // Add this line
                ] );

                // Create corresponding Saldo record
                $saldoController = new SaldoController();
                $saldoController->store ( [ 
                    'tipe'                     => $request->tipe,
                    'quantity'                 => $request->quantity,
                    'harga'                    => $request->harga,
                    'satuan'                   => $request->satuan, // Add this line
                    'id_proyek'                => $request->id_proyek,
                    'id_master_data_sparepart' => $request->id_master_data_sparepart,
                    'id_master_data_supplier'  => $request->id_master_data_supplier, // Add this line
                    'id_atb'                   => $atb->id
                ] );

                // Remove or comment out this line as it's not needed
                // dd ( $request->all () );
            }

            DB::commit ();
            return back ()->with ( 'success', 'Data ATB dan Saldo berhasil disimpan' );

        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            if ( isset ( $baseStoragePath ) )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $baseStoragePath );
            }
            return back ()->withErrors ( [ 'error' => 'Gagal menyimpan data ATB dan Saldo: ' . $e->getMessage () ] )->withInput ();
        }
    }

    public function destroy ( $id )
    {
        try
        {
            DB::beginTransaction ();

            $atb = ATB::findOrFail ( $id );

            if ( $atb->tipe === 'panjar-proyek' || $atb->tipe === 'panjar-unit-alat' )
            {
                // For panjar types, simply delete the ATB and associated records

                // Delete associated files
                if ( $atb->dokumentasi_foto )
                {
                    Storage::disk ( 'public' )->deleteDirectory ( $atb->dokumentasi_foto );
                }

                // Delete the associated Saldo record
                $saldo = Saldo::where ( 'id_atb', $atb->id )->first ();
                if ( $saldo )
                {
                    $saldoController = new SaldoController();
                    $saldoController->destroy ( $saldo->id );
                }

                // Delete the ATB record
                $atb->delete ();
            }

            if ( $atb->tipe === 'hutang-unit-alat' )
            {
                // For non-panjar types (existing logic for hutang)
                $suratTandaTerima = $atb->surat_tanda_terima;

                // Find all ATB records with the same Surat Tanda Terima
                $atbs = ATB::where ( 'surat_tanda_terima', $suratTandaTerima )->get ();

                $saldoController = new SaldoController();

                foreach ( $atbs as $atb )
                {
                    // Delete associated files
                    if ( $atb->dokumentasi_foto )
                    {
                        Storage::disk ( 'public' )->deleteDirectory ( $atb->dokumentasi_foto );
                    }

                    // Restore quantity_belum_diterima for the corresponding DetailSPB
                    $detailSpb = DetailSPB::find ( $atb->id_detail_spb );
                    $detailSpb->increaseQuantityBelumDiterima ( $atb->quantity );

                    // Delete the associated Saldo record
                    $saldo = Saldo::where ( 'id_atb', $atb->id )->first ();
                    if ( $saldo )
                    {
                        $saldoController->destroy ( $saldo->id );
                    }

                    // Delete the ATB record
                    $atb->delete ();
                }

                // Delete the shared surat_tanda_terima file
                if ( $suratTandaTerima )
                {
                    // Get the main ATB folder path by extracting the parent directory path
                    $mainFolderPath = dirname ( dirname ( $suratTandaTerima ) );

                    // Delete the entire ATB folder and all its contents
                    Storage::disk ( 'public' )->deleteDirectory ( $mainFolderPath );
                }
            }

            DB::commit ();

            return back ()->with ( 'success', 'Data ATB berhasil dihapus' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return back ()->withErrors ( [ 'error' => 'Gagal menghapus data ATB: ' . $e->getMessage () ] );
        }
    }

    public function getStt ( $id )
    {
        try
        {
            $atb = ATB::findOrFail ( $id );

            // Check if STT exists
            if ( ! $atb->surat_tanda_terima || ! file_exists ( storage_path ( 'app/public/' . $atb->surat_tanda_terima ) ) )
            {
                return response ()->json ( [ 
                    'success' => false,
                    'message' => 'STT tidak ditemukan'
                ], 404 );
            }

            // Return PDF URL
            return response ()->json ( [ 
                'success' => true,
                'pdf_url' => asset ( 'storage/' . $atb->surat_tanda_terima )
            ] );
        }
        catch ( \Exception $e )
        {
            return response ()->json ( [ 
                'success' => false,
                'message' => 'Error fetching STT: ' . $e->getMessage ()
            ], 500 );
        }
    }

    public function getDokumentasi ( $id )
    {
        $atb         = ATB::findOrFail ( $id );
        $dokumentasi = [];

        // Update the path to check the storage path instead of public path
        $storagePath = storage_path ( 'app/public/' . $atb->dokumentasi_foto );

        if ( $atb->dokumentasi_foto && is_dir ( $storagePath ) )
        {
            // Get all image files from directory
            $files = glob ( $storagePath . '/*.{jpg,jpeg,png}', GLOB_BRACE );

            foreach ( $files as $file )
            {
                // Convert full system path to relative public path
                $relativePath  = 'storage/' . $atb->dokumentasi_foto . '/' . basename ( $file );
                $dokumentasi[] = $relativePath;
            }
        }

        return response ()->json ( [ 
            'dokumentasi' => $dokumentasi
        ] );
    }

    public function acceptMutasi ( Request $request, $id )
    {
        try
        {
            // Validate the request
            $validated = $request->validate ( [ 
                'id_atb'        => 'required|exists:atb,id',
                'quantity'      => 'required|integer|min:1',
                'dokumentasi'   => 'required|array',
                'dokumentasi.*' => 'required|file|image|mimes:jpeg,png,jpg|max:2048'
            ] );

            DB::beginTransaction ();

            // Find the ATB record to update
            $atb = ATB::with ( [ 'apbMutasi.saldo' ] )->findOrFail ( $request->id_atb );

            if ( $atb->tipe !== 'mutasi-proyek' )
            {
                throw new \Exception( 'This operation is only valid for mutation type ATB' );
            }

            // Get the linked APB Mutasi and its Saldo
            $apbMutasi = $atb->apbMutasi;
            $saldo     = $apbMutasi->saldo;

            if ( ! $saldo )
            {
                throw new \Exception( 'No saldo record found for this mutation' );
            }

            // Validate quantity against APB Mutasi quantity
            if ( $request->quantity > $apbMutasi->quantity )
            {
                throw new \Exception( 'Quantity cannot exceed the mutated quantity' );
            }

            // Handle dokumentasi upload
            $folderName      = 'atb_' . date ( 'YmdHis' ) . '_' . uniqid ();
            $baseStoragePath = 'uploads/atb/' . $folderName;
            $docPath         = $baseStoragePath . '/dokumentasi';
            Storage::disk ( 'public' )->makeDirectory ( $docPath );

            // Process and store documentation photos
            if ( $request->hasFile ( 'dokumentasi' ) )
            {
                foreach ( $request->file ( 'dokumentasi' ) as $photo )
                {
                    $photoName      = pathinfo ( $photo->getClientOriginalName (), PATHINFO_FILENAME );
                    $photoExt       = $photo->getClientOriginalExtension ();
                    $photoTimestamp = now ()->format ( 'Y-m-d--H-i-s' );
                    $fileName       = "{$photoName}___{$photoTimestamp}.{$photoExt}";
                    $photo->storeAs ( $docPath, $fileName, 'public' );
                }
            }

            // Delete old dokumentasi if exists
            if ( $atb->dokumentasi_foto )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $atb->dokumentasi_foto );
            }

            // Update the existing ATB record
            $atb->update ( [ 
                'quantity'         => $request->quantity,
                'dokumentasi_foto' => $docPath
            ] );

            // Update or create Saldo record
            $saldoController = new SaldoController();
            $existingSaldo   = Saldo::where ( 'id_atb', $atb->id )->first ();

            if ( $existingSaldo )
            {
                $existingSaldo->update ( [ 
                    'quantity' => $request->quantity
                ] );
            }
            else
            {
                $saldoController->store ( [ 
                    'tipe'                     => 'mutasi-proyek',
                    'quantity'                 => $request->quantity,
                    'harga'                    => $atb->harga,
                    'id_proyek'                => $atb->id_proyek,
                    'id_asal_proyek'           => $atb->id_asal_proyek,
                    'id_master_data_sparepart' => $atb->id_master_data_sparepart,
                    'id_master_data_supplier'  => $atb->id_master_data_supplier,
                    'id_atb'                   => $atb->id,
                    'satuan'                   => $saldo->satuan
                ] );
            }

            $atb->apbMutasi->update ( [ 'status' => 'accepted' ] );

            $saldoAsal = Saldo::find ( $atb->apbMutasi->id_saldo );
            $saldoAsal->decrementQuantity ( $request->quantity );

            DB::commit ();
            return back ()->with ( 'success', 'Mutasi ATB berhasil diterima.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            // Clean up uploaded files if they exist
            if ( isset ( $baseStoragePath ) )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $baseStoragePath );
            }
            return back ()->withErrors ( [ 'error' => 'Gagal menerima mutasi ATB: ' . $e->getMessage () ] );
        }
    }

    public function rejectMutasi ( $id )
    {
        try
        {
            // Find ATB first to validate it exists
            $atb = ATB::findOrFail ( $id );

            DB::beginTransaction ();

            $atb->apbMutasi->update ( [ 'status' => 'rejected' ] );

            DB::commit ();
            return back ()->with ( 'success', 'Mutasi ATB berhasil ditolak.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            // Clean up uploaded files if they exist
            if ( isset ( $baseStoragePath ) )
            {
                Storage::disk ( 'public' )->deleteDirectory ( $baseStoragePath );
            }
            return back ()->withErrors ( [ 'error' => 'Gagal menolak mutasi ATB: ' . $e->getMessage () ] );
        }
    }
}