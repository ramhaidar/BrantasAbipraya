<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Models\MasterDataAlat;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AlatProyek;

class AlatProyekController extends Controller
{
    public function index ( Request $request )
    {
        $user = Auth::user ();

        $proyek = Proyek::with ( "users" )->find ( $request->id_proyek );

        if ( ! $proyek )
        {
            abort ( 404, "Proyek tidak ditemukan" );
        }

        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $AlatAssigned = AlatProyek::with ( "masterDataAlat" )
            ->where ( 'id_proyek', $proyek->id )
            ->whereNull ( 'removed_at' )
            ->orderBy ( 'updated_at', 'desc' )
            ->get ();

        $AlatAvailable = MasterDataAlat::whereDoesntHave ( 'alatProyek', function ($query)
        {
            $query->whereNull ( 'removed_at' );
        } )->get ();

        // Kirim data ke view
        return view ( 'dashboard.alat.alat', [ 
            'proyeks'       => $proyeks,
            'proyek'        => $proyek,
            'AlatAssigned'  => $AlatAssigned,
            'AlatAvailable' => $AlatAvailable,
            'headerPage'    => "Data Alat Proyek",
            'page'          => 'Data Alat',
        ] );
    }

    public function store ( Request $request )
    {
        $validatedData = $request->validate ( [ 
            'id_master_data_alat'   => 'required|array',
            'id_master_data_alat.*' => 'exists:master_data_alat,id',
            'id_proyek'             => 'required|exists:proyek,id',
        ] );

        foreach ( $validatedData[ 'id_master_data_alat' ] as $alatId )
        {
            AlatProyek::create ( [ 
                'id_master_data_alat' => $alatId,
                'id_proyek'           => $validatedData[ 'id_proyek' ],
                'assigned_at'         => now (),
            ] );
        }

        return redirect ()->back ()->with ( 'success', 'Alat berhasil ditambahkan ke proyek' );
    }

    public function destroy ( $id )
    {
        $alatProyek = AlatProyek::findOrFail ( $id );

        // Set removed_at timestamp instead of deleting the record
        $alatProyek->update ( [ 
            'removed_at' => now ()
        ] );

        return redirect ()->back ()->with ( 'success', 'Alat berhasil dihapus dari proyek' );
    }
}
