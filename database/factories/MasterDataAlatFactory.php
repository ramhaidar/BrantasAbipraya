<?php

namespace Database\Factories;

use App\Models\MasterDataAlat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterDataAlat>
 */
class MasterDataAlatFactory extends Factory
{
    protected $model = MasterDataAlat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition () : array
    {
        return [ 
            'jenis_alat'    => $this->faker->word,
            'kode_alat'     => $this->faker->unique ()->numerify ( 'KA###' ),
            'merek_alat'    => $this->faker->company,
            'tipe_alat'     => $this->faker->word,
            'serial_number' => $this->faker->unique ()->bothify ( 'SN-###-???' ),
        ];
    }
}
