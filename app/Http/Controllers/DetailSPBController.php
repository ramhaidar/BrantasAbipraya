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

    public function index ( $id )
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
        $selectedKategori  = $this->getSelectedValues ( request ( 'selected_kategori' ) ); // Move this here
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

                        $quantityMatch = true;
                        if ( ! empty ( $selectedQuantity ) )
                        {
                            $quantity = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                                $rkbDetail->detailRkbGeneral?->quantity_remainder ?? null;

                            $quantityMatch = in_array ( (string) $quantity, $selectedQuantity ) ||
                                ( in_array ( 'null', $selectedQuantity ) && $quantity === null );
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

        // Collect all unique values into single array - modified to only include items with remaining quantity > 0
        $uniqueValues = [];

        $uniqueValues[ 'jenis_alat' ] = $collection->flatMap ( function ($item)
        {
            return $item->linkAlatDetailRkbs->flatMap ( function ($detail)
            {
                $hasValidQuantity = $detail->linkRkbDetails->some ( function ($rkbDetail)
                {
                    $remainder = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                        $rkbDetail->detailRkbGeneral?->quantity_remainder ?? 0;
                    return $remainder > 0;
                } );
                return $hasValidQuantity ? [ $detail->masterDataAlat->jenis_alat ] : [];
            } );
        } )->unique ()->filter ()->sort ()->values ();

        $uniqueValues[ 'kode_alat' ] = $collection->flatMap ( function ($item)
        {
            return $item->linkAlatDetailRkbs->flatMap ( function ($detail)
            {
                $hasValidQuantity = $detail->linkRkbDetails->some ( function ($rkbDetail)
                {
                    $remainder = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                        $rkbDetail->detailRkbGeneral?->quantity_remainder ?? 0;
                    return $remainder > 0;
                } );
                return $hasValidQuantity ? [ $detail->masterDataAlat->kode_alat ] : [];
            } );
        } )->unique ()->filter ()->sort ()->values ();

        $uniqueValues[ 'kategori' ] = $collection->flatMap ( function ($item)
        {
            return $item->linkAlatDetailRkbs->flatMap ( function ($detail)
            {
                return $detail->linkRkbDetails->filter ( function ($rkbDetail)
                {
                    $remainder = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                        $rkbDetail->detailRkbGeneral?->quantity_remainder ?? 0;
                    return $remainder > 0;
                } )->map ( function ($rkbDetail)
                {
                    $kategori = $rkbDetail->detailRkbUrgent?->kategoriSparepart ??
                        $rkbDetail->detailRkbGeneral?->kategoriSparepart;
                    return $kategori ? $kategori->kode . ': ' . $kategori->nama : null;
                } );
            } );
        } )->unique ()->filter ()->sort ()->values ();

        $uniqueValues[ 'sparepart' ] = $collection->flatMap ( function ($item)
        {
            return $item->linkAlatDetailRkbs->flatMap ( function ($detail)
            {
                return $detail->linkRkbDetails->filter ( function ($rkbDetail)
                {
                    $remainder = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                        $rkbDetail->detailRkbGeneral?->quantity_remainder ?? 0;
                    return $remainder > 0;
                } )->map ( function ($rkbDetail)
                {
                    $sparepart = $rkbDetail->detailRkbUrgent?->masterDataSparepart ??
                        $rkbDetail->detailRkbGeneral?->masterDataSparepart;
                    return $sparepart ? $sparepart->nama . ' - ' . ( $sparepart->part_number ?? '-' ) . ' - ' . ( $sparepart->merk ?? '-' ) : null;
                } );
            } );
        } )->unique ()->filter ()->sort ()->values ();

        $uniqueValues[ 'quantity' ] = $collection->flatMap ( function ($item)
        {
            return $item->linkAlatDetailRkbs->flatMap ( function ($detail)
            {
                return $detail->linkRkbDetails->map ( function ($rkbDetail)
                {
                    $remainder = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                        $rkbDetail->detailRkbGeneral?->quantity_remainder ?? 0;
                    return $remainder > 0 ? $remainder : null;
                } );
            } );
        } )->unique ()->filter ()->sort ()->values ();

        $uniqueValues[ 'satuan' ] = $collection->flatMap ( function ($item)
        {
            return $item->linkAlatDetailRkbs->flatMap ( function ($detail)
            {
                return $detail->linkRkbDetails->filter ( function ($rkbDetail)
                {
                    $remainder = $rkbDetail->detailRkbUrgent?->quantity_remainder ??
                        $rkbDetail->detailRkbGeneral?->quantity_remainder ?? 0;
                    return $remainder > 0;
                } )->map ( function ($rkbDetail)
                {
                    return $rkbDetail->detailRkbUrgent?->satuan ??
                        $rkbDetail->detailRkbGeneral?->satuan ?? null;
                } );
            } );
        } )->unique ()->filter ()->sort ()->values ();

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
            'selectedKategori'  => $selectedKategori, // Now this will always be defined
            'selectedSparepart' => $selectedSparepart,
            'selectedQuantity'  => $selectedQuantity,
            'selectedSatuan'    => $selectedSatuan,
            'uniqueValues'      => $uniqueValues,
        ] );
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
        // Validate request
        $validated = $request->validate ( [ 
            'id_rkb'               => 'required|exists:rkb,id',
            'supplier_main'        => 'required|exists:master_data_supplier,id',
            'sparepart'            => 'required|array',
            'sparepart.*'          => 'required|exists:master_data_sparepart,id',
            'qty'                  => 'required|array',
            'qty.*'                => 'required|numeric|min:0',
            'harga'                => 'required|array',
            'harga.*'              => 'required|string',
            'satuan'               => 'required|array',
            'satuan.*'             => 'required|string',
            'alat_detail_id'       => 'required|array',
            'alat_detail_id.*'     => 'required|exists:link_alat_detail_rkb,id',
            'link_rkb_detail_id'   => 'required|array',
            'link_rkb_detail_id.*' => 'required|exists:link_rkb_detail,id',
            'spb_addendum_id'      => [ 'string', 'nullable' ],
        ] );

        \DB::beginTransaction ();

        try
        {
            if ( $validated[ 'spb_addendum_id' ] == null )
            {
                // Create new SPB
                $spb = SPB::create ( [ 
                    'nomor'                   => 'SPB-' . now ()->format ( 'YmdHis' ),
                    'is_addendum'             => false,
                    'id_master_data_supplier' => $validated[ 'supplier_main' ],
                    'tanggal'                 => now (),
                ] );

                $message = "SPB berhasil dibuat";
            }
            else
            {
                $originalSpb = SPB::findOrFail ( $validated[ 'spb_addendum_id' ] );

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
                $spb = SPB::create ( [ 
                    'nomor'                   => $nomorSPB,
                    'is_addendum'             => false,
                    'id_master_data_supplier' => $validated[ 'supplier_main' ],
                    'tanggal'                 => now (),
                    'id_spb_original'         => $originalSpb->id,
                ] );

                $message = "SPB berhasil di Addendum";
            }

            // Use the corrected relationship name
            $linkRKBSPB = $spb->linkRkbSpbs ()->create ( [ 
                'id_rkb' => $validated[ 'id_rkb' ],
                'id_spb' => $spb->id
            ] );

            // Loop through each sparepart item
            foreach ( $validated[ 'sparepart' ] as $index => $sparepartId )
            {
                $qty             = $validated[ 'qty' ][ $index ];
                $satuan          = $validated[ 'satuan' ][ $index ];
                $alatDetailId    = $validated[ 'alat_detail_id' ][ $index ];
                $linkRkbDetailId = $validated[ 'link_rkb_detail_id' ][ $index ];

                // Clean and extract numeric value from price string
                $harga = preg_replace ( '/[^0-9]/', '', $validated[ 'harga' ][ $index ] );

                // Skip if quantity is 0
                if ( $qty <= 0 ) continue;

                // Create DetailSPB with cleaned price and satuan
                $detailSPB = DetailSPB::create ( [ 
                    'quantity_po'              => $qty,
                    'harga'                    => (int) $harga,
                    'satuan'                   => $satuan,
                    'id_master_data_sparepart' => $sparepartId,
                    'id_master_data_alat'      => $alatDetailId,
                    'id_link_rkb_detail'       => $linkRkbDetailId,
                ] );

                // Create LinkSPBDetailSPB
                $spb->linkSpbDetailSpb ()->create ( [ 
                    'id_detail_spb' => $detailSPB->id
                ] );

                // Update quantity remainder in RKB detail using the correct link_rkb_detail_id
                $linkRKBDetail = LinkRKBDetail::findOrFail ( $linkRkbDetailId );
                $rkb           = RKB::findOrFail ( $validated[ 'id_rkb' ] );

                if ( $rkb->tipe === 'urgent' )
                {
                    $detail = $linkRKBDetail->detailRkbUrgent;
                }
                else
                {
                    $detail = $linkRKBDetail->detailRkbGeneral;
                }

                if ( ! $detail || $detail->quantity_remainder < $qty )
                {
                    throw new \Exception( "Quantity tidak valid untuk sparepart yang dipilih" );
                }

                $detail->decrementQuantityRemainder ( $qty );
            }

            \DB::commit ();
            return redirect ()->back ()->with ( 'success', $message );
        }
        catch ( \Exception $e )
        {
            \DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal membuat SPB: ' . $e->getMessage () );
        }
    }
}
