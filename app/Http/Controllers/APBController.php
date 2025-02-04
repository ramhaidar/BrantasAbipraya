<?php

namespace App\Http\Controllers;

use App\Models\APB;
use App\Models\ATB;
use App\Models\RKB;
use App\Models\Alat;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\AlatProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDataSparepart;
use App\Http\Controllers\Controller;

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
        // Validate and set perPage to allowed values only
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) request ()->get ( 'per_page' ), $allowedPerPage ) ? (int) request ()->get ( 'per_page' ) : 10;

        // Clean and format tipe
        $tipe = strtolower ( str_replace ( ' ', '-', $tipe ) );

        // Get search query
        $search = request ()->get ( 'search', '' );

        // Get base APB query with relationships
        $query = APB::with ( [ 
            'masterDataSparepart.kategoriSparepart',
            'masterDataSupplier',
            'proyek',
            'alatProyek.masterDataAlat',
            'saldo'
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
                            $q->orWhereRaw ( "DAYNAME(tanggal) = ?", [ $eng ] );
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
                    // Non-date search criteria
                    $q->where ( function ($q) use ($search)
                    {
                        $q->whereHas ( 'masterDataSparepart', function ($q) use ($search)
                        {
                            $q->where ( 'nama', 'like', "%{$search}%" )
                                ->orWhere ( 'part_number', 'like', "%{$search}%" )
                                ->orWhere ( 'merk', 'like', "%{$search}%" )
                                ->orWhereHas ( 'kategoriSparepart', function ($q) use ($search)
                                {
                                    $q->where ( 'kode', 'like', "%{$search}%" )
                                        ->orWhere ( 'nama', 'like', "%{$search}%" );
                                } );
                        } )
                            ->orWhereHas ( 'masterDataSupplier', function ($q) use ($search)
                            {
                                $q->where ( 'nama', 'like', "%{$search}%" );
                            } )
                            ->orWhereHas ( 'alatProyek.masterDataAlat', function ($q) use ($search)
                            {
                                $q->where ( 'jenis_alat', 'like', "%{$search}%" )
                                    ->orWhere ( 'kode_alat', 'like', "%{$search}%" )
                                    ->orWhere ( 'merek_alat', 'like', "%{$search}%" )
                                    ->orWhere ( 'tipe_alat', 'like', "%{$search}%" )
                                    ->orWhere ( 'serial_number', 'like', "%{$search}%" );
                            } )
                            // Add search for tujuan proyek
                            ->orWhereHas ( 'tujuanProyek', function ($q) use ($search)
                            {
                                $q->where ( 'nama', 'like', "%{$search}%" );
                            } )
                            // Add search for satuan in saldo
                            ->orWhereHas ( 'saldo', function ($q) use ($search)
                            {
                                $q->where ( 'satuan', 'like', "%{$search}%" );
                            } )
                            ->orWhere ( 'mekanik', 'like', "%{$search}%" );

                        // For numeric searches (quantity, price, total price)
                        if ( is_numeric ( str_replace ( [ ',', '.' ], '', $search ) ) )
                        {
                            $numericSearch = str_replace ( [ ',', '.' ], '', $search );
                            $q->orWhere ( 'quantity', 'like', "%{$numericSearch}%" )
                                ->orWhereHas ( 'saldo', function ($q) use ($numericSearch)
                                {
                                    $q->where ( 'harga', 'like', "%{$numericSearch}%" );
                                } )
                                // Add search for total price calculation
                                ->orWhereRaw ( '(
                                  SELECT s.harga * apb.quantity 
                                  FROM saldo s 
                                  WHERE s.id = apb.id_saldo
                              ) LIKE ?', [ "%{$numericSearch}%" ] );
                        }
                    } );
                }
            } );
        }

        // Get paginated results
        $TableData = $query->orderBy ( 'tanggal', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Get other required data
        $proyek  = Proyek::with ( "users" )->findOrFail ( $id_proyek );
        $alats   = AlatProyek::where ( 'id_proyek', $id_proyek )->get ();
        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        // Get filtered SPBs if needed
        $spbs = $this->getFilteredSpbs ( $id_proyek );

        // Restore spareparts logic for modal
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

        $sparepartsForMutasi = Saldo::where ( 'quantity', '>', 0 )
            ->with ( [ 'masterDataSparepart', 'atb' ] )
            ->whereHas ( 'atb', function ($query) use ($tipe, $id_proyek)
            {
                $query->where ( 'id_proyek', $id_proyek );
                if ( $tipe !== 'mutasi-proyek' )
                {
                    $query->where ( 'tipe', $tipe );
                }
            } )
            ->get ()
            ->sortBy ( 'atb.tanggal' );

        return view ( "dashboard.apb.apb", [ 
            "proyek"              => $proyek,
            "alats"               => $alats,
            "proyeks"             => $proyeks,
            "spbs"                => $spbs,
            "spareparts"          => $spareparts, // Restored
            "sparepartsForMutasi" => $sparepartsForMutasi, // Restored
            "headerPage"          => $proyek->nama,
            "page"                => $pageTitle,
            "tipe"                => $tipe,
            "TableData"           => $TableData,
            "search"              => $search
        ] );
    }

    // Helper method to get filtered SPBs
    private function getFilteredSpbs ( $id_proyek )
    {
        $rkbs = RKB::with ( "spbs.linkSpbDetailSpb.detailSpb" )
            ->where ( 'id_proyek', $id_proyek )
            ->get ();

        $spbs = collect ();
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

        return $spbs;
    }

    public function store ( Request $request )
    {
        // Validate request
        $validated = $request->validate ( [ 
            'tanggal'   => 'required|date',
            'id_proyek' => 'required|exists:proyek,id',
            'id_alat'   => 'required|exists:alat_proyek,id',
            'id_saldo'  => 'required|exists:saldo,id',
            'quantity'  => 'required|integer|min:1',
            'tipe'      => 'required|string',
            // Removed root_cause validation
            'mekanik'   => 'required|string|max:255'
        ] );

        try
        {
            // Start transaction
            DB::beginTransaction ();

            // Find the saldo with available quantity
            $saldo = Saldo::find ( $request->id_saldo );

            $masterDataSparepart = $saldo->masterDataSparepart;

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
                'id_master_data_sparepart' => $masterDataSparepart->id,
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

    public function mutasi_store ( Request $request )
    {
        $validated = $request->validate ( [ 
            'tanggal'          => 'required|date',
            'id_proyek'        => 'required|exists:proyek,id',
            'id_proyek_tujuan' => 'required|exists:proyek,id|different:id_proyek',
            'id_saldo'         => 'required|exists:saldo,id',
            'quantity'         => 'required|integer|min:1',
            'tipe'             => 'required|string',
            'keterangan'       => 'nullable|string'
        ] );

        try
        {
            DB::beginTransaction ();

            $saldo = Saldo::findOrFail ( $request->id_saldo );

            // Calculate pending quantity
            $pendingQuantity = APB::where ( 'id_saldo', $saldo->id )
                ->where ( 'tipe', 'mutasi-proyek' )
                ->where ( 'status', 'pending' )
                ->sum ( 'quantity' );

            // Calculate available quantity
            $availableQuantity = $saldo->quantity - $pendingQuantity;

            if ( $availableQuantity < $request->quantity )
            {
                throw new \Exception(
                    'Stok sparepart tidak mencukupi. Sisa stok yang tersedia: ' .
                    $availableQuantity . ' ' . $saldo->masterDataSparepart->satuan
                );
            }

            // Create APB record with pending status
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

            // Create ATB record for the destination project
            ATB::create ( [ 
                'tanggal'                  => $request->tanggal,
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

            return redirect ()->back ()
                ->with ( 'success', 'Mutasi sparepart berhasil dibuat dan menunggu persetujuan.' );
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()
                ->with ( 'error', 'Gagal melakukan mutasi: ' . $e->getMessage () );
        }
    }

    public function mutasi_destroy ( $id )
    {
        try
        {
            // Start transaction
            DB::beginTransaction ();

            // Find the APB record
            $apb = APB::findOrFail ( $id );

            if ( $apb->tipe !== 'mutasi-proyek' )
            {
                // Increment the quantity back to the saldo
                $apb->saldo->incrementQuantity ( $apb->quantity );
            }

            $apb->atbMutasi->delete ();

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

    public function destroy ( $id )
    {
        try
        {
            // Start transaction
            DB::beginTransaction ();

            // Find the APB record
            $apb = APB::findOrFail ( $id );

            if ( $apb->tipe !== 'mutasi-proyek' )
            {
                // Increment the quantity back to the saldo
                $apb->saldo->incrementQuantity ( $apb->quantity );
            }

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
