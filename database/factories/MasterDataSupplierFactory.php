<?php

namespace Database\Factories;

use App\Models\MasterDataSupplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterDataSupplier>
 */
class MasterDataSupplierFactory extends Factory
{
    protected $model = MasterDataSupplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition ()
    {
        return [ 
            'nama' => $this->faker->company (),
        ];
    }
}
