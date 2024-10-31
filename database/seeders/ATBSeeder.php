<?php
namespace Database\Seeders;

use App\Models\ATB;
use App\Models\Saldo;
use Illuminate\Database\Seeder;

class ATBSeeder extends Seeder
{
    public function run ()
    {
        ATB::factory ()->count ( 300 )->create ()->each ( function ($atb)
        {
            $saldo = Saldo::factory ()->create ( [ 
                // 'id_atb'           => $atb->id,
                'current_quantity' => $atb->quantity,
                'net'              => $atb->quantity * $atb->harga,
            ] );
            $atb->update ( [ 'id_saldo' => $saldo->id ] );
        } );
    }
}
