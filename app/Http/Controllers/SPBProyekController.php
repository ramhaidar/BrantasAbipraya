<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SPBProyekController extends Controller
{
    //

    public function index ()
    {
        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "asc" )
            ->get ();
        // $rkbs    = RKB::where ( 'is_approved', true )->get ();

        return view ( 'dashboard.spb.proyek.proyek', [ 
            'proyeks'    => $proyeks,
            // 'rkbs'       => $rkbs,

            'headerPage' => "SPB Proyek",
            'page'       => 'Data SPB Proyek',
        ] );
    }

    public function getData ( Request $request )
    {
        $query = RKB::with ( 'proyek' )
            ->where ( 'is_approved_vp', true )
            ->where ( 'is_approved_svp', true )
            ->select ( 'rkb.*' );

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

        $totalRecords    = RKB::where ( 'is_approved_vp', true )
            ->where ( 'is_approved_svp', true )
            ->count ();
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
                'tipe'    => ucfirst ( $item->tipe ),
            ];
        } );

        return response ()->json ( [ 
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ] );
    }
}
