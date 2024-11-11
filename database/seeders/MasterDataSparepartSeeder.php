<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterDataSparepart;

class MasterDataSparepartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run ()
    {
        MasterDataSparepart::factory ()->count ( 300 )->create ();
    }
}
