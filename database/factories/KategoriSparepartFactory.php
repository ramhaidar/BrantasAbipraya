<?php

namespace Database\Factories;

use App\Models\KategoriSparepart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriSparepart>
 */
class KategoriSparepartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = KategoriSparepart::class;

    public function definition ()
    {
        return [ 
            'kode'      => strtoupper ( $this->faker->unique ()->lexify ( '???' ) ),
            'nama'      => $this->faker->word,
            'jenis'     => null,
            'sub_jenis' => null,
        ];
    }
}
