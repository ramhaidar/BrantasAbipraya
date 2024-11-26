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
            $isFinalized = $item->is_finalized ?? false;
            $isApproved = $item->is_approved ?? false;

            $status = match ( true )
            {
                $isFinalized && $isApproved => 'Disetujui',
                ! $isFinalized => 'Pengajuan',
                $isFinalized && ! $isApproved => 'Evaluasi',
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
