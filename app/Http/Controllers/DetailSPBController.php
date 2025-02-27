<?php

namespace App\Http\Controllers;

use App\Models\RKB;
use App\Models\SPB;
use App\Models\Proyek;
use App\Models\DetailSPB;
use Illuminate\Http\Request;
use App\Models\LinkRKBDetail;
use App\Models\MasterDataSupplier;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class DetailSPBController extends Controller
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

    /**
     * Apply numeric filters for fields like quantity
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param string $paramName
     * @param array $values
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function applyNumericFilter ( $collection, $paramName, $values )
    {
        if ( empty ( $values ) )
        {
            return $collection;
        }

        return $collection->map ( function ($item) use ($paramName, $values)
        {
            $filtered = clone $item;

            // Check for numeric filters for quantity
            $hasGtFilter        = false;
            $hasLtFilter        = false;
            $gtValue            = null;
            $ltValue            = null;
            $exactValues        = [];
            $hasEmptyNullFilter = false;

            // First pass to categorize values
            foreach ( $values as $value )
            {
                if ( strpos ( $value, 'gt:' ) === 0 )
                {
                    $hasGtFilter = true;
                    $gtValue     = (float) substr ( $value, 3 );
                }
                elseif ( strpos ( $value, 'lt:' ) === 0 )
                {
                    $hasLtFilter = true;
                    $ltValue     = (float) substr ( $value, 3 );
                }
                elseif ( strpos ( $value, 'exact:' ) === 0 )
                {
                    $exactValues[] = (float) substr ( $value, 6 );
                }
                elseif ( $value === 'Empty/Null' )
                {
                    $hasEmptyNullFilter = true;
                }
                elseif ( is_numeric ( $value ) )
                {
                    $exactValues[] = (float) $value;
                }
            }

            $filtered->linkAlatDetailRkbs = $filtered->linkAlatDetailRkbs->map ( function ($detail) use ($paramName, $hasGtFilter, $hasLtFilter, $gtValue, $ltValue, $exactValues, $hasEmptyNullFilter)
            {
                // Clone the detail to avoid modifying original
                $detailClone = clone $detail;

                // Filter linkRkbDetails based on quantity
                if ( $paramName === 'quantity' )
                {
                    $detailClone->linkRkbDetails = $detail->linkRkbDetails->filter ( function ($rkbDetail) use ($hasGtFilter, $hasLtFilter, $gtValue, $ltValue, $exactValues, $hasEmptyNullFilter)
                    {
                        $quantity = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                            $rkbDetail->detailRkbGeneral?->quantity_remainder ?? null;

                        // Range filter (gt AND lt)
                        if ( $hasGtFilter && $hasLtFilter )
                        {
                            return ( $quantity >= $gtValue && $quantity <= $ltValue ) ||
                                ( $hasEmptyNullFilter && ( $quantity === null || $quantity === 0 ) );
                        }

                        // Individual gt filter
                        if ( $hasGtFilter && ! $hasLtFilter )
                        {
                            return ( $quantity >= $gtValue ) ||
                                ( $hasEmptyNullFilter && ( $quantity === null || $quantity === 0 ) );
                        }

                        // Individual lt filter
                        if ( $hasLtFilter && ! $hasGtFilter )
                        {
                            return ( $quantity <= $ltValue ) ||
                                ( $hasEmptyNullFilter && ( $quantity === null || $quantity === 0 ) );
                        }

                        // Exact value filter
                        if ( ! empty ( $exactValues ) )
                        {
                            return in_array ( $quantity, $exactValues ) ||
                                ( $hasEmptyNullFilter && ( $quantity === null || $quantity === 0 ) );
                        }

                        // Only Empty/Null filter
                        if ( $hasEmptyNullFilter )
                        {
                            return ( $quantity === null || $quantity === 0 );
                        }

                        return true;
                    } );
                }

                return $detailClone;
            } )->filter ();

            return $filtered;
        } );
    }

    public function index ( Request $request, $id )
    {
        // Get single RKB record with relationships
        $rkb = RKB::with ( [ 
            "linkAlatDetailRkbs.masterDataAlat",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart" => fn ( $query ) => $query->orderBy ( 'nama' ),
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.kategoriSparepart",
            "linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.masterDataSparepart"  => fn ( $query ) => $query->orderBy ( 'nama' ),
            "linkAlatDetailRkbs.timelineRkbUrgents",
            "linkAlatDetailRkbs.lampiranRkbUrgent",
            "proyek",
            "spbs"
        ] )->findOrFail ( $id );

        // Get selected filter values
        $selectedJenisAlat = $this->getSelectedValues ( request ( 'selected_jenis_alat' ) );
        $selectedKodeAlat  = $this->getSelectedValues ( request ( 'selected_kode_alat' ) );
        $selectedKategori  = $this->getSelectedValues ( request ( 'selected_kategori' ) );
        $selectedSparepart = $this->getSelectedValues ( request ( 'selected_sparepart' ) );
        $selectedQuantity  = $this->getSelectedValues ( request ( 'selected_quantity' ) );
        $selectedSatuan    = $this->getSelectedValues ( request ( 'selected_satuan' ) );

        // Create collection from RKB
        $collection = collect ( [ $rkb ] );

        // Apply filters if selected
        if ( ! empty ( $selectedJenisAlat ) || ! empty ( $selectedKodeAlat ) || ! empty ( $selectedKategori ) || ! empty ( $selectedSparepart ) || ! empty ( $selectedQuantity ) || ! empty ( $selectedSatuan ) )
        {
            $collection = $collection->map ( function ($item) use ($selectedJenisAlat, $selectedKodeAlat, $selectedKategori, $selectedSparepart, $selectedQuantity, $selectedSatuan)
            {
                $filtered = clone $item;

                $filtered->linkAlatDetailRkbs = $filtered->linkAlatDetailRkbs->map ( function ($detail) use ($selectedJenisAlat, $selectedKodeAlat, $selectedKategori, $selectedSparepart, $selectedQuantity, $selectedSatuan)
                {
                    $jenisAlatMatch = empty ( $selectedJenisAlat ) ||
                        in_array ( $detail->masterDataAlat->jenis_alat, $selectedJenisAlat ) ||
                        ( in_array ( 'null', $selectedJenisAlat ) && empty ( $detail->masterDataAlat->jenis_alat ) );

                    $kodeAlatMatch = empty ( $selectedKodeAlat ) ||
                        in_array ( $detail->masterDataAlat->kode_alat, $selectedKodeAlat ) ||
                        ( in_array ( 'null', $selectedKodeAlat ) && empty ( $detail->masterDataAlat->kode_alat ) );

                    // Clone the detail to avoid modifying original
                    $detailClone = clone $detail;

                    // Filter linkRkbDetails based on kategori
                    $detailClone->linkRkbDetails = $detail->linkRkbDetails->filter ( function ($rkbDetail) use ($selectedKategori, $selectedSparepart, $selectedQuantity, $selectedSatuan)
                    {
                        $kategoriMatch = true;
                        if ( ! empty ( $selectedKategori ) )
                        {
                            $kategori = $rkbDetail->detailRkbGeneral?->kategoriSparepart ??
                                $rkbDetail->detailRkbUrgent?->kategoriSparepart;

                            $kategoriValue = $kategori ? $kategori->kode . ': ' . $kategori->nama : null;

                            $kategoriMatch = in_array ( $kategoriValue, $selectedKategori ) ||
                                ( in_array ( 'null', $selectedKategori ) && empty ( $kategoriValue ) );
                        }

                        $sparepartMatch = true;
                        if ( ! empty ( $selectedSparepart ) )
                        {
                            $sparepart = $rkbDetail->detailRkbGeneral?->masterDataSparepart ??
                                $rkbDetail->detailRkbUrgent?->masterDataSparepart;

                            $sparepartValue = $sparepart ?
                                $sparepart->nama . ' - ' . ( $sparepart->part_number ?? '-' ) . ' - ' . ( $sparepart->merk ?? '-' ) :
                                null;

                            $sparepartMatch = in_array ( $sparepartValue, $selectedSparepart ) ||
                                ( in_array ( 'null', $selectedSparepart ) && empty ( $sparepartValue ) );
                        }

                        // Handle numeric quantity filter with prefix parsing
                        $quantityMatch = true;
                        if ( ! empty ( $selectedQuantity ) )
                        {
                            $quantity = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                                $rkbDetail->detailRkbGeneral?->quantity_remainder ?? null;

                            // Check for special prefixes
                            $hasGtFilter        = false;
                            $hasLtFilter        = false;
                            $gtValue            = null;
                            $ltValue            = null;
                            $exactValues        = [];
                            $hasEmptyNullFilter = false;

                            foreach ( $selectedQuantity as $value )
                            {
                                if ( strpos ( $value, 'gt:' ) === 0 )
                                {
                                    $hasGtFilter = true;
                                    $gtValue = (float) substr ( $value, 3 );
                                }
                                elseif ( strpos ( $value, 'lt:' ) === 0 )
                                {
                                    $hasLtFilter = true;
                                    $ltValue = (float) substr ( $value, 3 );
                                }
                                elseif ( strpos ( $value, 'exact:' ) === 0 )
                                {
                                    $exactValues[] = (float) substr ( $value, 6 );
                                }
                                elseif ( $value === 'Empty/Null' )
                                {
                                    $hasEmptyNullFilter = true;
                                }
                                elseif ( is_numeric ( $value ) )
                                {
                                    $exactValues[] = (float) $value;
                                }
                            }

                            // Apply filters based on type
                            if ( $hasGtFilter && $hasLtFilter )
                            {
                                $quantityMatch = ( $quantity >= $gtValue && $quantity <= $ltValue ) ||
                                    ( $hasEmptyNullFilter && ( $quantity === null || $quantity === 0 ) );
                            }
                            elseif ( $hasGtFilter )
                            {
                                $quantityMatch = ( $quantity >= $gtValue ) ||
                                    ( $hasEmptyNullFilter && ( $quantity === null || $quantity === 0 ) );
                            }
                            elseif ( $hasLtFilter )
                            {
                                $quantityMatch = ( $quantity <= $ltValue ) ||
                                    ( $hasEmptyNullFilter && ( $quantity === null || $quantity === 0 ) );
                            }
                            elseif ( ! empty ( $exactValues ) )
                            {
                                $quantityMatch = in_array ( $quantity, $exactValues ) ||
                                    ( $hasEmptyNullFilter && ( $quantity === null || $quantity === 0 ) );
                            }
                            elseif ( $hasEmptyNullFilter )
                            {
                                $quantityMatch = ( $quantity === null || $quantity === 0 );
                            }
                        }

                        $satuanMatch = true;
                        if ( ! empty ( $selectedSatuan ) )
                        {
                            $satuan = $rkbDetail->detailRkbUrgent?->satuan ??
                                $rkbDetail->detailRkbGeneral?->satuan ?? null;

                            $satuanMatch = in_array ( $satuan, $selectedSatuan ) ||
                                ( in_array ( 'null', $selectedSatuan ) && $satuan === null );
                        }

                        return $kategoriMatch && $sparepartMatch && $quantityMatch && $satuanMatch;
                    } );

                    // Only keep this alat if it matches alat filters and has any matching spareparts
                    if ( ( $jenisAlatMatch && $kodeAlatMatch ) && $detailClone->linkRkbDetails->isNotEmpty () )
                    {
                        return $detailClone;
                    }
                    return null;
                } )->filter (); // Remove null entries

                return $filtered;
            } );
        }

        // Get unique values from database directly (not affected by current filters)
        $uniqueValues = $this->getAllUniqueValues ( $id );

        // Create paginator from filtered collection
        $TableData = new LengthAwarePaginator(
            $collection,
            1,
            1,
            1
        );

        // Calculate total items for SPB creation
        $totalItems = $rkb->linkAlatDetailRkbs->sum ( function ($detail1)
        {
            return $detail1->linkRkbDetails->sum ( function ($detail2)
            {
                $remainder = $detail2->detailRkbUrgent?->quantity_remainder ?? $detail2->detailRkbGeneral?->quantity_remainder ?? 0;
                return $remainder > 0 ? 1 : 0;
            } );
        } );

        // Get SPB history and addendum data
        $riwayatSpb = SPB::with ( 'linkSpbDetailSpb' )
            ->whereIn ( 'id', $rkb->spbs ()->pluck ( 'id_spb' ) )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->get ();

        $spbAddendumEd = SPB::whereIn ( 'id', $rkb->spbs ()->pluck ( 'id_spb' ) )
            ->where ( 'is_addendum', true )
            ->where ( function ($query)
            {
                $query->where ( 'nomor', 'not like', '%-1' )
                    ->where ( 'nomor', 'not like', '%-2' )
                    ->where ( 'nomor', 'not like', '%-3' )
                    ->where ( 'nomor', 'not like', '%-4' )
                    ->where ( 'nomor', 'not like', '%-5' )
                    ->where ( 'nomor', 'not like', '%-6' )
                    ->where ( 'nomor', 'not like', '%-7' )
                    ->where ( 'nomor', 'not like', '%-8' )
                    ->where ( 'nomor', 'not like', '%-9' );
            } )
            ->get ();

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

        return view ( 'dashboard.spb.detail.detail', [ 
            'proyeks'           => $proyeks,
            'rkb'               => $rkb, // Keep original RKB for detail form
            'TableData'         => $TableData, // Paginated data for consistency
            'supplier'          => MasterDataSupplier::all (),
            'totalItems'        => $totalItems,
            'riwayatSpb'        => $riwayatSpb,
            'spbAddendumEd'     => $spbAddendumEd,
            'headerPage'        => "SPB Supplier",
            'page'              => 'Detail SPB Supplier' . ' [' . $rkb->proyek->nama . ' | ' . $rkb->nomor . ' ]',
            'selectedJenisAlat' => $selectedJenisAlat,
            'selectedKodeAlat'  => $selectedKodeAlat,
            'selectedKategori'  => $selectedKategori,
            'selectedSparepart' => $selectedSparepart,
            'selectedQuantity'  => $selectedQuantity,
            'selectedSatuan'    => $selectedSatuan,
            'uniqueValues'      => $uniqueValues,
        ] );
    }

    /**
     * Get all unique values for filters directly from the database
     * regardless of current table filters
     */
    private function getAllUniqueValues ( $rkbId )
    {
        $rkb          = RKB::findOrFail ( $rkbId );
        $uniqueValues = [];

        // First load the complete RKB with all its relationships to use Eloquent's power
        $fullRkb = RKB::with ( [ 
            'linkAlatDetailRkbs.masterDataAlat',
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart',
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart',
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.kategoriSparepart',
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent.masterDataSparepart',
        ] )->findOrFail ( $rkbId );

        // Get jenis_alat values
        $uniqueValues[ 'jenis_alat' ] = $fullRkb->linkAlatDetailRkbs->pluck ( 'masterDataAlat.jenis_alat' )
            ->filter () // Remove empty values
            ->unique ()
            ->sort ()
            ->values ();

        // Get kode_alat values
        $uniqueValues[ 'kode_alat' ] = $fullRkb->linkAlatDetailRkbs->pluck ( 'masterDataAlat.kode_alat' )
            ->filter ()
            ->unique ()
            ->sort ()
            ->values ();

        // Get kategori values - only for items with quantity > 0
        $kategoriValues = collect ();

        // From general details
        $generalKategoriValues = $fullRkb->linkAlatDetailRkbs->flatMap ( function ($alatDetail)
        {
            return $alatDetail->linkRkbDetails->filter ( function ($rkbDetail)
            {
                // Only include if general detail exists and has remainder > 0
                return $rkbDetail->detailRkbGeneral && $rkbDetail->detailRkbGeneral->quantity_remainder > 0;
            } )->map ( function ($rkbDetail)
            {
                $kategori = $rkbDetail->detailRkbGeneral->kategoriSparepart;
                return $kategori ? $kategori->kode . ': ' . $kategori->nama : null;
            } );
        } );

        // From urgent details
        $urgentKategoriValues = $fullRkb->linkAlatDetailRkbs->flatMap ( function ($alatDetail)
        {
            return $alatDetail->linkRkbDetails->filter ( function ($rkbDetail)
            {
                // Only include if urgent detail exists and has remainder > 0
                return $rkbDetail->detailRkbUrgent && $rkbDetail->detailRkbUrgent->quantity_remainder > 0;
            } )->map ( function ($rkbDetail)
            {
                $kategori = $rkbDetail->detailRkbUrgent->kategoriSparepart;
                return $kategori ? $kategori->kode . ': ' . $kategori->nama : null;
            } );
        } );

        $uniqueValues[ 'kategori' ] = $kategoriValues->concat ( $generalKategoriValues )
            ->concat ( $urgentKategoriValues )
            ->filter ()
            ->unique ()
            ->sort ()
            ->values ();

        // Get sparepart values - only for items with quantity > 0
        $sparepartValues = collect ();

        // From general details
        $generalSparepartValues = $fullRkb->linkAlatDetailRkbs->flatMap ( function ($alatDetail)
        {
            return $alatDetail->linkRkbDetails->filter ( function ($rkbDetail)
            {
                // Only include if general detail exists and has remainder > 0
                return $rkbDetail->detailRkbGeneral && $rkbDetail->detailRkbGeneral->quantity_remainder > 0;
            } )->map ( function ($rkbDetail)
            {
                $sparepart = $rkbDetail->detailRkbGeneral->masterDataSparepart;
                return $sparepart ? $sparepart->nama . ' - ' . ( $sparepart->part_number ?? '-' ) . ' - ' . ( $sparepart->merk ?? '-' ) : null;
            } );
        } );

        // From urgent details
        $urgentSparepartValues = $fullRkb->linkAlatDetailRkbs->flatMap ( function ($alatDetail)
        {
            return $alatDetail->linkRkbDetails->filter ( function ($rkbDetail)
            {
                // Only include if urgent detail exists and has remainder > 0
                return $rkbDetail->detailRkbUrgent && $rkbDetail->detailRkbUrgent->quantity_remainder > 0;
            } )->map ( function ($rkbDetail)
            {
                $sparepart = $rkbDetail->detailRkbUrgent->masterDataSparepart;
                return $sparepart ? $sparepart->nama . ' - ' . ( $sparepart->part_number ?? '-' ) . ' - ' . ( $sparepart->merk ?? '-' ) : null;
            } );
        } );

        $uniqueValues[ 'sparepart' ] = $sparepartValues->concat ( $generalSparepartValues )
            ->concat ( $urgentSparepartValues )
            ->filter ()
            ->unique ()
            ->sort ()
            ->values ();

        // Get quantity values - only for items with quantity > 0
        $quantityValues = collect ();

        // From general details
        $generalQuantityValues = $fullRkb->linkAlatDetailRkbs->flatMap ( function ($alatDetail)
        {
            return $alatDetail->linkRkbDetails->map ( function ($rkbDetail)
            {
                return $rkbDetail->detailRkbGeneral && $rkbDetail->detailRkbGeneral->quantity_remainder > 0
                    ? $rkbDetail->detailRkbGeneral->quantity_remainder
                    : null;
            } );
        } );

        // From urgent details
        $urgentQuantityValues = $fullRkb->linkAlatDetailRkbs->flatMap ( function ($alatDetail)
        {
            return $alatDetail->linkRkbDetails->map ( function ($rkbDetail)
            {
                return $rkbDetail->detailRkbUrgent && $rkbDetail->detailRkbUrgent->quantity_remainder > 0
                    ? $rkbDetail->detailRkbUrgent->quantity_remainder
                    : null;
            } );
        } );

        $uniqueValues[ 'quantity' ] = $quantityValues->concat ( $generalQuantityValues )
            ->concat ( $urgentQuantityValues )
            ->filter ()
            ->unique ()
            ->sort ()
            ->values ();

        // Get satuan values - only for items with quantity > 0
        $satuanValues = collect ();

        // From general details
        $generalSatuanValues = $fullRkb->linkAlatDetailRkbs->flatMap ( function ($alatDetail)
        {
            return $alatDetail->linkRkbDetails->filter ( function ($rkbDetail)
            {
                return $rkbDetail->detailRkbGeneral && $rkbDetail->detailRkbGeneral->quantity_remainder > 0;
            } )->map ( function ($rkbDetail)
            {
                return $rkbDetail->detailRkbGeneral->satuan;
            } );
        } );

        // From urgent details
        $urgentSatuanValues = $fullRkb->linkAlatDetailRkbs->flatMap ( function ($alatDetail)
        {
            return $alatDetail->linkRkbDetails->filter ( function ($rkbDetail)
            {
                return $rkbDetail->detailRkbUrgent && $rkbDetail->detailRkbUrgent->quantity_remainder > 0;
            } )->map ( function ($rkbDetail)
            {
                return $rkbDetail->detailRkbUrgent->satuan;
            } );
        } );

        $uniqueValues[ 'satuan' ] = $satuanValues->concat ( $generalSatuanValues )
            ->concat ( $urgentSatuanValues )
            ->filter ()
            ->unique ()
            ->sort ()
            ->values ();

        return $uniqueValues;
    }

    public function getSparepart ( $idSupplier )
    {
        try
        {
            $supplier = MasterDataSupplier::with ( 'masterDataSpareparts' )->find ( $idSupplier );

            return response ()->json ( $supplier );
        }
        catch ( \Exception $e )
        {
            return response ()->json ( [] );
        }
    }

    public function store ( Request $request )
    {
        // Basic validation for required fields
        $request->validate ( [ 
            'id_rkb'          => 'required|exists:rkb,id',
            'supplier_main'   => 'required|exists:master_data_supplier,id',
            'spb_addendum_id' => [ 'nullable', 'string' ],
        ] );

        // Get all submitted data
        $data = $request->all ();

        // Begin transaction to ensure database integrity
        DB::beginTransaction ();

        try
        {
            // First, check if any spareparts are selected (at least one row should be filled)
            $hasSelectedSpareparts = false;

            if ( ! isset ( $data[ 'sparepart' ] ) || empty ( $data[ 'sparepart' ] ) )
            {
                throw ValidationException::withMessages ( [ 
                    'sparepart' => [ 'Minimal harus memilih satu sparepart' ]
                ] );
            }

            // Create a clean validated array to hold only valid row data
            $validatedRows = [];

            // Iterate through each submitted sparepart to validate only filled rows
            foreach ( $data[ 'sparepart' ] as $key => $sparepartId )
            {
                // Only validate rows where the sparepart is selected
                if ( ! empty ( $sparepartId ) )
                {
                    $hasSelectedSpareparts = true;

                    // Verify that related fields for this specific row exist
                    if ( ! isset ( $data[ 'qty' ][ $key ] ) )
                    {
                        throw ValidationException::withMessages ( [ 
                            'qty' => [ "Quantity tidak ditemukan untuk sparepart yang dipilih" ]
                        ] );
                    }

                    if ( ! isset ( $data[ 'harga' ][ $key ] ) )
                    {
                        throw ValidationException::withMessages ( [ 
                            'harga' => [ "Harga tidak ditemukan untuk sparepart yang dipilih" ]
                        ] );
                    }

                    if ( ! isset ( $data[ 'satuan' ][ $key ] ) )
                    {
                        throw ValidationException::withMessages ( [ 
                            'satuan' => [ "Satuan tidak ditemukan untuk sparepart yang dipilih" ]
                        ] );
                    }

                    // Validate quantity and price for selected spareparts only
                    $qty   = (float) $data[ 'qty' ][ $key ];
                    $harga = filter_var ( $data[ 'harga' ][ $key ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

                    // Skip rows with zero or negative quantities
                    if ( $qty <= 0 )
                    {
                        continue;
                    }

                    // Add to validated rows
                    $validatedRows[ $key ] = [ 
                        'sparepart_id'       => $sparepartId,
                        'qty'                => $qty,
                        'harga'              => (float) $harga,
                        'satuan'             => $data[ 'satuan' ][ $key ],
                        'alat_detail_id'     => $data[ 'alat_detail_id' ][ $key ],
                        'link_rkb_detail_id' => $data[ 'link_rkb_detail_id' ][ $key ],
                    ];
                }
            }

            // Ensure at least one valid row with positive quantity exists
            if ( empty ( $validatedRows ) )
            {
                throw ValidationException::withMessages ( [ 
                    'qty' => [ 'Minimal satu sparepart harus memiliki quantity lebih dari 0' ]
                ] );
            }

            // Create SPB with correct addendum handling
            if ( empty ( $data[ 'spb_addendum_id' ] ) )
            {
                // Create new SPB
                $spb     = SPB::create ( [ 
                    'nomor'                   => 'SPB-' . now ()->format ( 'YmdHis' ),
                    'is_addendum'             => false,
                    'id_master_data_supplier' => $data[ 'supplier_main' ],
                    'tanggal'                 => now (),
                ] );
                $message = "SPB berhasil dibuat";
            }
            else
            {
                $originalSpb = SPB::findOrFail ( $data[ 'spb_addendum_id' ] );

                // Get base SPB number
                $baseNumber = $originalSpb->nomor;

                // Find the highest increment for this base number
                $highestIncrement = SPB::where ( 'nomor', 'ilike', $baseNumber . '-%' )
                    ->get ()
                    ->map ( function ($item) use ($baseNumber)
                    {
                        if ( preg_match ( '/-(\d+)$/', $item->nomor, $matches ) )
                        {
                            return (int) $matches[ 1 ];
                        }
                        return 0;
                    } )
                    ->max ();

                $nomorSPB = $baseNumber . '-' . ( $highestIncrement + 1 );

                // Create new SPB
                $spb     = SPB::create ( [ 
                    'nomor'                   => $nomorSPB,
                    'is_addendum'             => false,
                    'id_master_data_supplier' => $data[ 'supplier_main' ],
                    'tanggal'                 => now (),
                    'id_spb_original'         => $originalSpb->id,
                ] );
                $message = "SPB berhasil di Addendum";
            }

            // Create RKB-SPB link
            $linkRKBSPB = $spb->linkRkbSpbs ()->create ( [ 
                'id_rkb' => $data[ 'id_rkb' ],
                'id_spb' => $spb->id
            ] );

            // Process validated rows
            foreach ( $validatedRows as $index => $rowData )
            {
                // For safety, check max quantity again when processing data
                $linkRKBDetail = LinkRKBDetail::findOrFail ( $rowData[ 'link_rkb_detail_id' ] );
                $rkb           = RKB::findOrFail ( $data[ 'id_rkb' ] );

                if ( $rkb->tipe === 'urgent' )
                {
                    $detail = $linkRKBDetail->detailRkbUrgent;
                }
                else
                {
                    $detail = $linkRKBDetail->detailRkbGeneral;
                }

                if ( ! $detail )
                {
                    throw new \Exception( "Detail RKB tidak ditemukan" );
                }

                // Verify the quantity doesn't exceed the remainder
                $remainder = $detail->quantity_remainder;
                if ( $rowData[ 'qty' ] > $remainder )
                {
                    throw new \Exception( "Quantity PO ({$rowData[ 'qty' ]}) melebihi Quantity Sisa ({$remainder}) untuk sparepart yang dipilih" );
                }

                // Create DetailSPB
                $detailSPB = DetailSPB::create ( [ 
                    'quantity_po'              => $rowData[ 'qty' ],
                    'harga'                    => $rowData[ 'harga' ],
                    'satuan'                   => $rowData[ 'satuan' ],
                    'id_master_data_sparepart' => $rowData[ 'sparepart_id' ],
                    'id_master_data_alat'      => $rowData[ 'alat_detail_id' ],
                    'id_link_rkb_detail'       => $rowData[ 'link_rkb_detail_id' ],
                ] );

                // Create LinkSPBDetailSPB
                $spb->linkSpbDetailSpb ()->create ( [ 
                    'id_detail_spb' => $detailSPB->id
                ] );

                // Decrement the quantity remainder
                $detail->decrementQuantityRemainder ( $rowData[ 'qty' ] );
            }

            DB::commit ();
            return redirect ()->back ()->with ( 'success', $message );
        }
        catch ( ValidationException $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->withErrors ( $e->errors () )->withInput ();
        }
        catch ( \Exception $e )
        {
            DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal membuat SPB: ' . $e->getMessage () )->withInput ();
        }
    }
}
