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
                UsersTableSeeder::class,
                AlatSeeder::class,
                ProyekSeeder::class,
                    // ATBSeeder::class,
                    // APBSeeder::class,
                    // MasterDataSeeder::class,
                MasterDataAlatSeeder::class,
                MasterDataSparepartSeeder::class,
                MasterDataSupplierSeeder::class,
            ]
        );
    }
}
