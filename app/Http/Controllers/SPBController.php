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

class SPBController extends Controller
{
    // Index for SPB
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get ();
        // $rkbs    = RKB::where ( 'is_approved', true )->get ();

        return view ( 'dashboard.spb.spb', [ 
            'proyeks'    => $proyeks,
            // 'rkbs'       => $rkbs,

            'headerPage' => "SPB Supplier",
            'page'       => 'Data SPB Supplier',
        ] );
    }

    public function getData ( Request $request )
    {
        $query = RKB::with ( 'proyek' )->where ( 'is_approved', true )->select ( 'rkb.*' );

        // Filter pencarian
        if ( $search = $request->input ( 'search.value' ) )
        {
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nomor', 'like', "%{$search}%" )
                    ->orWhereHas ( 'proyek', function ($q) use ($search)
                    {
                        $q->where ( 'nama', 'like', "%{$search}%" );
                    } )
                    ->orWhere ( 'periode', 'like', "%{$search}%" );
            } );
        }

        // Sorting
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];

            if ( in_array ( $columnName, [ 'nomor', 'periode' ] ) )
            {
                $query->orderBy ( $columnName, $sortDirection );
            }
            elseif ( $columnName === 'proyek' )
            {
                $query->join ( 'proyek', 'rkb.id_proyek', '=', 'proyek.id' )
                    ->orderBy ( 'proyek.nama', $sortDirection );
            }
        }
        else
        {
            $query->orderBy ( 'updated_at', 'desc' );
        }

        // Pagination
        $draw   = $request->input ( 'draw' );
        $start  = $request->input ( 'start', 0 );
        $length = $request->input ( 'length', 10 );

        $totalRecords    = RKB::where ( 'is_approved', true )->count (); // Total hanya untuk data "Disetujui"
        $filteredRecords = $query->count ();

        $rkbData = $query->skip ( $start )->take ( $length )->get ();

        // Mapping data
        $data = $rkbData->map ( function ($item)
        {
            return [ 
                'id'      => $item->id,
                'nomor'   => $item->nomor,
                'proyek'  => $item->proyek->nama ?? '-',
                'periode' => Carbon::parse ( $item->periode )->translatedFormat ( 'F Y' ),
            ];
        } );

        return response ()->json ( [ 
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ] );
    }

    protected function console ( $message )
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln ( $message );
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
                    $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbGeneral->increment ( "quantity_remainder", $linkSpbDetailSpb->detailSpb->quantity );
                }

                if ( isset ( $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbUrgent ) )
                {
                    $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbUrgent->increment ( "quantity_remainder", $linkSpbDetailSpb->detailSpb->quantity );
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
                    $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbGeneral->increment ( "quantity_remainder", $linkSpbDetailSpb->detailSpb->quantity );
                }

                if ( isset ( $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbUrgent ) )
                {
                    $linkSpbDetailSpb->detailSpb->linkRkbDetail->detailRkbUrgent->increment ( "quantity_remainder", $linkSpbDetailSpb->detailSpb->quantity );
                }
            }

            \DB::commit ();
            return redirect ()->back ()->with ( 'success', 'SPB berhasil dihapus' );

        }
        catch ( \Exception $e )
        {
            \DB::rollBack ();
            return redirect ()->back ()->with ( 'error', 'Gagal menghapus SPB: ' . $e->getMessage () );
        }
    }
}