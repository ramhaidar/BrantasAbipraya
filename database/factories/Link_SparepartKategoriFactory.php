<?php

namespace Database\Factories;

use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link_SparepartKategori>
 */
class Link_SparepartKategoriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition () : array
    {
        return [ 
            'id_kategori'  => KategoriSparepart::query ()->inRandomOrder ()->value ( 'id' ),
            'id_sparepart' => MasterDataSparepart::query ()->inRandomOrder ()->value ( 'id' ),
        ];
    }
}
