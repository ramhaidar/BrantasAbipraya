<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\SPB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\LinkRKBDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SPBController extends Controller
{
    public function index ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $user = Auth::user ();

        // Filter projects based on user role
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

        $query = RKB::query ()
            ->with ( [ 'proyek', 'spbs' ] )
            ->where ( 'is_approved_svp', true );

        // Add project filtering for koordinator_proyek
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyekIds = $proyeks->pluck ( 'id' )->toArray ();
            $query->whereIn ( 'id_proyek', $proyekIds );
        }

        // Handle nomor filter
        if ( $request->filled ( 'selected_nomor' ) )
        {
            try
            {
                $nomor = $this->getSelectedValues ( $request->selected_nomor );
                if ( in_array ( 'null', $nomor ) )
                {
                    $nonNullValues = array_filter ( $nomor, fn ( $value ) => $value !== 'null' );
                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        $q->whereNull ( 'nomor' )
                            ->orWhere ( 'nomor', '-' )
                            ->orWhereIn ( 'nomor', $nonNullValues );
                    } );
                }
                else
                {
                    $query->whereIn ( 'nomor', $nomor );
                }
            }
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in nomor filter: ' . $e->getMessage () );
            }
        }

        // Handle proyek filter
        if ( $request->filled ( 'selected_proyek' ) )
        {
            try
            {
                $proyekNames = $this->getSelectedValues ( $request->selected_proyek );
                if ( in_array ( 'null', $proyekNames ) )
                {
                    $nonNullValues = array_filter ( $proyekNames, fn ( $value ) => $value !== 'null' );
                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        $q->whereDoesntHave ( 'proyek' )
                            ->orWhereHas ( 'proyek', function ($sq) use ($nonNullValues)
                            {
                                $sq->whereIn ( 'nama', $nonNullValues );
                            } );
                    } );
                }
                else
                {
                    $query->whereHas ( 'proyek', function ($q) use ($proyekNames)
                    {
                        $q->whereIn ( 'nama', $proyekNames );
                    } );
                }
            }
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in proyek filter: ' . $e->getMessage () );
            }
        }

        // Handle periode filter
        if ( $request->filled ( 'selected_periode' ) )
        {
            try
            {
                $periodeValues = $this->getSelectedValues ( $request->selected_periode );
                if ( in_array ( 'null', $periodeValues ) )
                {
                    $nonNullValues = array_filter ( $periodeValues, fn ( $value ) => $value !== 'null' );
                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        $q->whereNull ( 'periode' )
                            ->orWhereIn ( 'periode', $nonNullValues );
                    } );
                }
                else
                {
                    $query->whereIn ( 'periode', $periodeValues );
                }
            }
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in periode filter: ' . $e->getMessage () );
            }
        }

        // Handle tipe filter
        if ( $request->filled ( 'selected_tipe' ) )
        {
            try
            {
                $tipeValues = $this->getSelectedValues ( $request->selected_tipe );
                $query->whereIn ( 'tipe', $tipeValues );
            }
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in tipe filter: ' . $e->getMessage () );
            }
        }

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nomor', 'ilike', "%{$search}%" )
                    ->orWhereHas ( 'proyek', function ($query) use ($search)
                    {
                        $query->where ( 'nama', 'ilike', "%{$search}%" );
                    } )
                    ->orWhere ( function ($q) use ($search)
                    {
                        // Handle year search (4 digits)
                        if ( preg_match ( '/^[0-9]{4}$/', $search ) )
                        {
                            $q->whereYear ( 'periode', $search );
                        }
                        // Handle month name in Indonesian or English
                        elseif ( $this->isMonthName ( $search ) )
                        {
                            $monthNumber = $this->getMonthNumber ( $search );
                            if ( $monthNumber )
                            {
                                $q->whereMonth ( 'periode', $monthNumber );
                            }
                        }
                        // Handle "Month Year" format (e.g., "January 2023" or "Januari 2023")
                        elseif ( preg_match ( '/^([A-Za-z]+)\s+([0-9]{4})$/', $search, $matches ) )
                        {
                            $monthNumber = $this->getMonthNumber ( $matches[ 1 ] );
                            if ( $monthNumber )
                            {
                                $q->whereMonth ( 'periode', $monthNumber )
                                    ->whereYear ( 'periode', $matches[ 2 ] );
                            }
                        }
                    } );

                // Add separate type search
                $searchLower = strtolower ( $search );
                if ( $searchLower === 'urgent' || $searchLower === 'general' )
                {
                    $q->orWhereHas ( 'linkAlatDetailRkbs.linkRkbDetails', function ($query) use ($searchLower)
                    {
                        if ( $searchLower === 'urgent' )
                        {
                            $query->whereHas ( 'detailRkbUrgent' );
                        }
                        else
                        {
                            $query->whereHas ( 'detailRkbGeneral' );
                        }
                    } );
                }
            } );
        }

        $TableData = $query
            ->orderBy ( 'periode', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Get unique values for filters
        $uniqueValues = [ 
            'nomor'   => $query->clone ()
                ->select ( 'nomor' )
                ->whereNotNull ( 'nomor' )
                ->distinct ()
                ->reorder ()
                ->orderBy ( 'nomor', 'asc' )
                ->pluck ( 'nomor' ),
            'proyek'  => Proyek::whereIn ( 'id', function ($subquery) use ($query)
            {
                $subquery->select ( 'id_proyek' )
                    ->fromSub ( $query->clone ()->select ( 'id_proyek' ), 'filtered_rkb' )
                    ->whereNotNull ( 'id_proyek' )
                    ->distinct ();
            } )
                ->orderBy ( 'nama' )
                ->pluck ( 'nama' ),
            'periode' => $query->clone ()
                ->select ( 'periode' )
                ->whereNotNull ( 'periode' )
                ->distinct ()
                ->reorder ()
                ->orderBy ( 'periode', 'desc' )
                ->pluck ( 'periode' ),
            'tipe'    => [ 'general', 'urgent' ],
        ];

        return view ( 'dashboard.spb.spb', [ 
            'headerPage'   => "SPB Supplier",
            'page'         => 'Data SPB Supplier',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    /**
     * Check if the given string is a month name
     */
    private function isMonthName ( $string )
    {
        $months = array_merge (
            // Indonesian month names
            [ 
                'januari',
                'februari',
                'maret',
                'april',
                'mei',
                'juni',
                'juli',
                'agustus',
                'september',
                'oktober',
                'november',
                'desember'
            ],
            // English month names
            [ 
                'january',
                'february',
                'march',
                'april',
                'may',
                'june',
                'july',
                'august',
                'september',
                'october',
                'november',
                'december'
            ]
        );

        return in_array ( strtolower ( $string ), $months );
    }

    /**
     * Get month number from month name
     */
    private function getMonthNumber ( $monthName )
    {
        $monthMap = [ 
            // Indonesian
            'januari'   => 1,
            'februari'  => 2,
            'maret'     => 3,
            'april'     => 4,
            'mei'       => 5,
            'juni'      => 6,
            'juli'      => 7,
            'agustus'   => 8,
            'september' => 9,
            'oktober'   => 10,
            'november'  => 11,
            'desember'  => 12,
            // English
            'january'   => 1,
            'february'  => 2,
            'march'     => 3,
            'april'     => 4,
            'may'       => 5,
            'june'      => 6,
            'july'      => 7,
            'august'    => 8,
            'september' => 9,
            'october'   => 10,
            'november'  => 11,
            'december'  => 12
        ];

        return $monthMap[ strtolower ( $monthName ) ] ?? null;
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

    public function destroy ( $id )
    {
        try
        {
            \DB::beginTransaction ();

            // Get SPB with all necessary relationships
            $spb = SPB::with ( [ 
                'linkSpbDetailSpb.detailSpb',
                'linkRkbSpbs.rkb.linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent',
                'linkRkbSpbs.rkb.linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral'
            ] )->findOrFail ( $id );

            foreach ( $spb->linkSpbDetailSpb as $linkSpbDetailSpb )
            {
                if ( isset ( $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbGeneral ) )
                {
                    $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbGeneral->incrementQuantityRemainder ( $linkSpbDetailSpb->detailSpb->quantity_po );
                }

                if ( isset ( $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbUrgent ) )
                {
                    $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbUrgent->incrementQuantityRemainder ( $linkSpbDetailSpb->detailSpb->quantity_po );
                }

                $linkSpbDetailSpb->detailSpb->delete ();
            }

            // Delete LinkSPBDetailSPB and LinkRkbSpbs
            $spb->linkSpbDetailSpb ()->delete ();
            $spb->linkRkbSpbs ()->delete ();

            // Delete SPB
            $spb->delete ();

            \DB::commit ();
            return redirect ()->back ()->with ( 'success', 'SPB berhasil dihapus' );
        }
        catch ( \Exception $e )
        {
            \DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal menghapus SPB: ' . $e->getMessage () );
        }
    }

    public function addendum ( $id )
    {
        try
        {
            \DB::beginTransaction ();

            // Get SPB with all necessary relationships
            $spb = SPB::with ( [ 
                'linkSpbDetailSpb.detailSpb',
                'linkRkbSpbs.rkb.linkAlatDetailRkbs.linkRkbDetails.detailRkbUrgent',
                'linkRkbSpbs.rkb.linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral'
            ] )->findOrFail ( $id );

            $spb->is_addendum = true;
            $spb->save ();

            foreach ( $spb->linkSpbDetailSpb as $linkSpbDetailSpb )
            {
                if ( isset ( $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbGeneral ) )
                {
                    $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbGeneral->incrementQuantityRemainder ( $linkSpbDetailSpb->detailSpb->quantity_po );
                }

                if ( isset ( $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbUrgent ) )
                {
                    $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbUrgent->incrementQuantityRemainder ( $linkSpbDetailSpb->detailSpb->quantity_po );
                }
            }

            \DB::commit ();
            return redirect ()->back ()->with ( 'success', 'SPB berhasil di Addendum' );
        }
        catch ( \Exception $e )
        {
            \DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal melakukan Addendum SPB: ' . $e->getMessage () );
        }
    }
}