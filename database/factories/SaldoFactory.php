<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SaldoFactory extends Factory
{
    public function definition ()
    {
        return [ 
            // 'id_atb'           => null,
            // 'id_apb'           => null,
            'current_quantity' => null,
            'net'              => null,
        ];
    }
}
