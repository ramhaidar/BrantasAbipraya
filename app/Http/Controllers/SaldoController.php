<?php

namespace App\Http\Controllers;

use App\Models\Saldo;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function store ( $data )
    {
        try
        {
            // Handle store logic based on tipe
            $saldoData = [ 
                'tipe'                     => $data[ 'tipe' ],
                'quantity'                 => $data[ 'quantity' ],
                'harga'                    => $data[ 'harga' ],
                'id_proyek'                => $data[ 'id_proyek' ],
                'id_spb'                   => $data[ 'id_spb' ],
                'id_master_data_sparepart' => $data[ 'id_master_data_sparepart' ],
                'id_master_data_supplier'  => $data[ 'id_master_data_supplier' ]
            ];

            // Only set id_asal_proyek if type is 'mutasi-proyek'
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
}
