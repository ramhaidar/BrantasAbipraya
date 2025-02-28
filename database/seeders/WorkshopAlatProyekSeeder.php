<?php

namespace Database\Seeders;

use App\Models\MasterDataAlat;
use App\Models\Proyek;
use App\Models\AlatProyek;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkshopAlatProyekSeeder extends Seeder
{
    /**
     * Connect Workshop equipment with all projects
     */
    public function run () : void
    {
        // Find Workshop MasterDataAlat
        $workshop = MasterDataAlat::where ( 'kode_alat', 'Workshop' )->first ();

        if ( ! $workshop )
        {
            $this->command->warn ( 'Workshop entry not found in MasterDataAlat! Please run MasterDataAlatSeeder first.' );
            return;
        }

        // Get all projects
        $proyeks = Proyek::all ();

        if ( $proyeks->isEmpty () )
        {
            $this->command->warn ( 'No projects found! Please seed projects first.' );
            return;
        }

        $now   = Carbon::now ();
        $count = 0;

        // Connect Workshop with each project
        foreach ( $proyeks as $proyek )
        {
            // Check if connection already exists
            $exists = AlatProyek::where ( 'id_proyek', $proyek->id )
                ->where ( 'id_master_data_alat', $workshop->id )
                ->exists ();

            if ( ! $exists )
            {
                AlatProyek::create ( [ 
                    'id_proyek'           => $proyek->id,
                    'id_master_data_alat' => $workshop->id,
                    'assigned_at'         => $now,
                    'removed_at'          => null, // Ensure it's active
                ] );

                $count++;
            }
        }

        $this->command->info ( "Workshop connected to {$count} projects" );
    }
}
