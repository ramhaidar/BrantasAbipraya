<?php
namespace Database\Seeders;

use App\Models\APB;
use App\Models\ATB;
use App\Models\Saldo;
use Illuminate\Database\Seeder;

class APBSeeder extends Seeder
{
    public function run () : void
    {
        $atbs         = ATB::all ();
        $jumlahApb    = ceil ( $atbs->count () / 3 );
        $atbsTerpilih = $atbs->random ( $jumlahApb );

        foreach ( $atbsTerpilih as $atb )
        {
            $apb   = APB::factory ()->state ( [ 
                'quantity' => rand ( 1, $atb->quantity ),
                'id_saldo' => $atb->id_saldo,
            ] )->create ();
            $saldo = Saldo::where ( 'id', $atb->id_saldo )->first ();
            if ( $saldo )
            {
                $saldo->update ( [ 
                    // 'id_apb'           => $apb->id,
                    'current_quantity' => $saldo->current_quantity - $apb->quantity,
                ] );
            }
        }
    }
}
