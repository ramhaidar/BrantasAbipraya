<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run () : void
    {
        $this->call (
            [ 
                UserSeeder::class,
                ProyekSeeder::class,
                KategoriSparepartSeeder::class,
                MasterDataAlatSeeder::class,
                WorkshopAlatProyekSeeder::class,
                MasterDataSupplierSeeder::class,
                    // MasterDataSparepartSeeder::class,

                    // _BudongBudongSeeder::class,
                    // _BulangoSeeder::class,
                    // _BenerPaket4Seeder::class,
                ATBSeeder::class,
                MasterDataSparepartSeeder2::class,
            ]
        );
    }
}
