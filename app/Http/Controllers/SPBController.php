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

        $query = RKB::query ()
            ->with ( [ 'proyek', 'spbs' ] )
            ->where ( 'is_approved_svp', true );

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nomor', 'like', "%{$search}%" )
                    ->orWhereHas ( 'proyek', function ($query) use ($search)
                    {
                        $query->where ( 'nama', 'like', "%{$search}%" );
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
                    } )
                    // Add type search functionality
                    ->orWhere ( function ($q) use ($search)
                    {
                        $searchLower = strtolower ( $search );
                        if ( in_array ( $searchLower, [ 'general', 'urgent' ] ) )
                        {
                            $q->where ( 'tipe', ucfirst ( $searchLower ) );
                        }
                    } );
            } );
        }

        $user = auth ()->user ();

        if ( $user->role === 'Pegawai' )
        {
            $query->whereHas ( 'proyek', function ($q) use ($user)
            {
                $q->whereHas ( 'users', function ($q) use ($user)
                {
                    $q->where ( 'users.id', $user->id );
                } );
            } );
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks       = $user->proyek ()->with ( "users" )->get ();
            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $query->whereHas ( 'proyek', function ($q) use ($usersInProyek)
            {
                $q->whereHas ( 'users', function ($q) use ($usersInProyek)
                {
                    $q->whereIn ( 'users.id', $usersInProyek );
                } );
            } );
        }

        $TableData = $query
            ->orderBy ( 'periode', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // dd ( $TableData );

        $proyeks = [];
        if ( $user->role !== 'Pegawai' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "updated_at", "desc" )
                ->orderBy ( "id", "desc" )
                ->get ();
        }

        return view ( 'dashboard.spb.spb', [ 
            'headerPage' => "SPB Supplier",
            'page'       => 'Data SPB Supplier',
            'proyeks'    => $proyeks,
            'TableData'  => $TableData,
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