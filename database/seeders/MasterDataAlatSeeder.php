<?php

namespace Database\Seeders;

use App\Models\MasterDataAlat;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterDataAlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run ()
    {
        MasterDataAlat::factory ()->count ( 300 )->create ();
    }
}
