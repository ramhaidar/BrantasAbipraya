<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterData>
 */
class MasterDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition () : array
    {
        // Cari user pertama dengan role 'Pegawai'
        $user = User::where ( 'role', 'Pegawai' )->inRandomOrder ()->first ();

        return [ 
            'supplier'     => $this->faker->company (),  // Menghasilkan nama perusahaan acak
            'sparepart'    => $this->faker->word (),    // Menghasilkan kata acak sebagai nama sparepart
            'part_number'  => $this->faker->unique ()->bothify ( 'PN-#####' ), // Nomor part acak
            'buffer_stock' => $this->faker->numberBetween ( 10, 100 ), // Stok acak antara 10 sampai 100
            'id_user'      => $user ? $user->id : null, // Mengisi id_user
        ];
    }
}
