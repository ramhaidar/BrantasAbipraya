<?php

namespace App\Http\Controllers;

use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SaldoController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showSaldoPage (
            "Hutang Unit Alat",
            "Data Saldo EX Unit Alat",
            $request->id_proyek
        );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showSaldoPage (
            "Panjar Unit Alat",
            "Data Saldo EX Panjar Unit Alat",
            $request->id_proyek
        );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showSaldoPage (
            "Mutasi Proyek",
            "Data Saldo EX Mutasi Saldo",
            $request->id_proyek
        );
    }


    public function panjar_proyek ( Request $request )
    {
        return $this->showSaldoPage (
            "Panjar Proyek",
            "Data Saldo EX Panjar Proyek",
            $request->id_proyek
        );
    }

    private function showSaldoPage ( $tipe, $pageTitle, $id_proyek )
    {
        $tipe = strtolower ( str_replace ( ' ', '-', $tipe ) );

        $proyek  = Proyek::with ( "users" )->find ( $id_proyek );
        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "asc" )
            ->orderBy ( "id", "asc" )
            ->get ();

        $saldos = Saldo::where ( 'id_proyek', $id_proyek )->where ( 'tipe', $tipe )->get ();

        return view ( "dashboard.saldo.saldo", [ 
            "proyek"     => $proyek,
            "proyeks"    => $proyeks,
            "saldos"     => $saldos,
            "headerPage" => $proyek->nama_proyek,
            "page"       => $pageTitle,
        ] );
    }

    public function store ( $data )
    {
        try
        {
            $saldoData = [ 
                'tipe'                     => $data[ 'tipe' ],
                'quantity'                 => $data[ 'quantity' ],
                'harga'                    => $data[ 'harga' ],
                'id_proyek'                => $data[ 'id_proyek' ],
                'id_spb'                   => $data[ 'id_spb' ],
                'id_master_data_sparepart' => $data[ 'id_master_data_sparepart' ],
                'id_master_data_supplier'  => $data[ 'id_master_data_supplier' ],
                'id_atb'                   => $data[ 'id_atb' ], // New column
                'satuan'                   => $data[ 'satuan' ]  // New column
            ];

            if ( $data[ 'tipe' ] === 'mutasi-proyek' && isset ( $data[ 'id_asal_proyek' ] ) )
            {
                $saldoData[ 'id_asal_proyek' ] = $data[ 'id_asal_proyek' ];
            }

            Saldo::create ( $saldoData );
            return true;
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }

    public function destroy ( $id )
    {
        try
        {
            $saldo = Saldo::findOrFail ( $id );
            $saldo->delete ();
            return true;
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }
}
