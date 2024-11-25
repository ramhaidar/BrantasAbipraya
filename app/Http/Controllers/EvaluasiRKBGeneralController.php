<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EvaluasiRKBGeneralController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::orderByDesc ( 'updated_at' )->get ();

        return view ( 'dashboard.evaluasi.general.evaluasi_general', [ 
            'proyeks'    => $proyeks,

            'headerPage' => "Evaluasi General",
            'page'       => 'Data Evaluasi General',
        ] );
    }

    public function getData ( Request $request )
    {
        $query = RKB::with ( 'proyek' )->select ( 'rkb.*' );

        // Filter untuk pencarian
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

        // Filter untuk pengurutan
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

        // Pagination dan data
        $draw   = $request->input ( 'draw' );
        $start  = $request->input ( 'start', 0 );
        $length = $request->input ( 'length', 10 );

        $totalRecords    = RKB::count ();
        $filteredRecords = $query->count ();

        $rkbData = $query->skip ( $start )->take ( $length )->get ();

        $data = $rkbData->map ( function ($item)
        {
            return [ 
                'id'           => $item->id,
                'nomor'        => $item->nomor,
                'proyek'       => $item->proyek->nama ?? '-',
                'periode'      => Carbon::parse ( $item->periode )->translatedFormat ( 'F Y' ),
                'is_finalized' => $item->is_finalized,
                'is_approved'  => $item->is_approved,
            ];
        } );

        return response ()->json ( [ 
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data
        ] );
    }
}
