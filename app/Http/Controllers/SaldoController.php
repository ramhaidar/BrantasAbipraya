<?php

namespace App\Http\Controllers;

use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SaldoController extends Controller
{
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

        // Calculate total amount for all records
        // Clone the query before adding pagination to get accurate total
        $totalQuery  = clone $query;
        $totalAmount = $totalQuery->sum ( \DB::raw ( 'saldo.quantity * saldo.harga' ) );

        // Get paginated results with proper perPage value
        $TableData = $query->join ( 'atb', 'saldo.id_atb', '=', 'atb.id' )
            ->select ( 'saldo.*' )
            ->orderBy ( 'atb.tanggal', 'desc' )
            ->orderBy ( 'saldo.updated_at', 'desc' )
            ->orderBy ( 'saldo.id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Add total amount to pagination object
        $TableData->total_amount = $totalAmount;

        $proyek  = Proyek::with ( "users" )->findOrFail ( $id_proyek );
        $proyeks = Proyek::with ( "users" )
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
            "allowedPerPage" => $allowedPerPage, // Add this to view
            "perPage"        => $perPage // Add this to view
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
            if ( $data[ 'tipe' ] === 'hutang-unit-alat' )
            {
                $saldoData[ 'id_spb' ] = $data[ 'id_spb' ];
                $saldoData[ 'satuan' ] = $data[ 'satuan' ];
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
