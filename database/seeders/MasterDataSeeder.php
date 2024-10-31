<?php

namespace Database\Seeders;

use App\Models\MasterData;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run () : void
    {
        MasterData::factory ()->count ( 50 )->create ();
    }
}
