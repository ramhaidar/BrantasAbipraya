<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EvaluasiRKBUrgentController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        return view ( 'dashboard.evaluasi.urgent.evaluasi_urgent', [ 
            'proyeks'     => $proyeks,

            'headerPage'  => "Evaluasi Urgent",
            'page'        => 'Data Evaluasi Urgent',

            'menuContext' => 'evaluasi_urgent', // Add this flag
        ] );

    }

    public function getData ( Request $request )
    {
        $query = RKB::with ( 'proyek' )->where ( 'tipe', 'Urgent' )->select ( 'rkb.*' );

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
                    ->orWhere ( 'periode', 'like', "%{$search}%" )
                    ->orWhereRaw (
                        "CASE 
                    WHEN is_finalized = 1 AND is_approved = 1 THEN 'Disetujui'
                    WHEN is_finalized = 0 THEN 'Pengajuan'
                    WHEN is_finalized = 1 AND is_approved = 0 THEN 'Evaluasi'
                    WHEN is_finalized = 1 AND is_evaluated = 1 AND is_approved = 0 THEN 'Menunggu Approval'
                    ELSE 'Tidak Diketahui'
                  END LIKE ?",
                        [ "%{$search}%" ]
                    );
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

        $totalRecords    = RKB::count ();
        $filteredRecords = $query->count ();

        $rkbData = $query->skip ( $start )->take ( $length )->get ();

        // Mapping data
        $data = $rkbData->map ( function ($item)
        {
            $isFinalized   = $item->is_finalized ?? false;
            $isEvaluated   = $item->is_evaluated ?? false;
            $isApprovedVp  = $item->is_approved_vp ?? false;
            $isApprovedSvp = $item->is_approved_svp ?? false;

            $status = match ( true )
            {
                $isFinalized && $isEvaluated && $isApprovedVp && $isApprovedSvp => 'Disetujui',
                ! $isFinalized => 'Pengajuan',
                $isFinalized && $isEvaluated && $isApprovedVp && ! $isApprovedSvp => 'Menunggu Approval SVP',
                $isFinalized && $isEvaluated && ! $isApprovedVp => 'Menunggu Approval VP',
                $isFinalized && ! $isEvaluated => 'Evaluasi',

                default => 'Tidak Diketahui',
            };

            return [ 
                'id'      => $item->id,
                'nomor'   => $item->nomor,
                'proyek'  => $item->proyek->nama ?? '-',
                'periode' => Carbon::parse ( $item->periode )->translatedFormat ( 'F Y' ),
                'status'  => $status,
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
