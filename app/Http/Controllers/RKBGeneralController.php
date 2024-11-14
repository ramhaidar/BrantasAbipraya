<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RKBGeneralController extends Controller
{
    public function index ()
    {
        $proyeks = Proyek::orderByDesc ( 'updated_at' )->get ();

        return view ( 'dashboard.rkb.general.general', [ 
            'proyeks'    => $proyeks,
            'headerPage' => "RKB General",
            'page'       => 'Data RKB General',
        ] );
    }

    public function detail_index ( $id )
    {
        $rkb = RKB::with ( [ 'proyek', 'linkRkbDetails' ] )->find ( $id );
        dd ( $rkb );
    }

    public function show ( $id )
    {
        $rkb = RKB::with ( [ 'proyek', 'linkRkbDetails' ] )->find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_general.index' )->with ( 'error', 'RKB Tidak Ditemukan.' );
        }

        return response ()->json ( [ 'data' => $rkb ] );
    }

    public function store ( Request $request )
    {
        // Validasi data request
        $validatedData = $request->validate ( [ 
            'nomor'   => 'required|string|max:255',
            'periode' => [ 'required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/' ],
            'proyek'  => 'required|integer|exists:proyek,id',
        ] );

        // Tambahkan hari default (26) agar sesuai dengan tipe DATE di database
        $validatedData[ 'periode' ] = $validatedData[ 'periode' ] . '-26';

        // Pastikan kolom 'proyek' dipetakan ke 'id_proyek'
        $validatedData[ 'id_proyek' ] = $validatedData[ 'proyek' ];
        unset ( $validatedData[ 'proyek' ] ); // Hapus field 'proyek' karena tidak ada di tabel

        // Simpan data ke tabel RKB
        RKB::create ( $validatedData );

        // Redirect dengan pesan sukses
        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully created' );
    }
    public function update ( Request $request, $id )
    {
        $rkb = RKB::find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_general.index' )->with ( 'error', 'RKB not found' );
        }

        // Modify the request to map 'proyek' to 'id_proyek'
        $request->merge ( [ 'id_proyek' => $request->proyek ] );

        // Validate the request
        $validatedData = $request->validate ( [ 
            'nomor'     => 'sometimes|required|string|max:255',
            'periode'   => 'sometimes|required|date',
            'id_proyek' => 'sometimes|required|integer|exists:proyek,id',
        ] );

        // Update the RKB record
        $rkb->update ( $validatedData );

        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully updated' );
    }

    public function destroy ( $id )
    {
        $rkb = RKB::find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_general.index' )->with ( 'error', 'RKB not found' );
        }

        $rkb->delete ();

        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully deleted' );
    }

    public function getData ( Request $request )
    {
        $query = RKB::with ( 'proyek' )->select ( 'rkb.*' );

        // Handle search input
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

        // Handle ordering
        if ( $order = $request->input ( 'order' ) )
        {
            $columnIndex   = $order[ 0 ][ 'column' ];
            $columnName    = $request->input ( 'columns' )[ $columnIndex ][ 'data' ];
            $sortDirection = $order[ 0 ][ 'dir' ];

            // Ensure column exists in the table
            if ( in_array ( $columnName, [ 'nomor', 'periode' ] ) )
            {
                $query->orderBy ( $columnName, $sortDirection );
            }
            elseif ( $columnName === 'proyek' )
            {
                $query->join ( 'proyeks', 'rkb.id_proyek', '=', 'proyeks.id' )
                    ->orderBy ( 'proyeks.nama', $sortDirection );
            }
        }
        else
        {
            $query->orderBy ( 'updated_at', 'desc' );
        }

        // Calculate pagination parameters
        $draw   = $request->input ( 'draw' );
        $start  = $request->input ( 'start', 0 );
        $length = $request->input ( 'length', 10 );

        // Get total records count and filtered count
        $totalRecords    = RKB::count ();
        $filteredRecords = $query->count ();

        // Fetch the data with pagination
        $rkbData = $query->skip ( $start )->take ( $length )->get ();

        // Transform data for DataTables
        $data = $rkbData->map ( function ($item)
        {
            return [ 
                'nomor'   => $item->nomor,
                'proyek'  => $item->proyek->nama ?? '-',
                'periode' => Carbon::parse ( $item->periode )->translatedFormat ( 'F Y' ),
                'id'      => $item->id
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
