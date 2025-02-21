<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'master_data_sparepart', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'nama' );
            $table->string ( 'part_number' ); // Removed unique constraint
            $table->string ( 'merk' );
            $table->foreignId ( 'id_kategori_sparepart' )
                ->nullable ()
                ->constrained ( 'kategori_sparepart' )
                ->nullOnDelete ();

            $table->timestamps ();
        } );

        // Add partial unique index
        DB::statement ( 'CREATE UNIQUE INDEX part_number_unique ON master_data_sparepart (part_number) WHERE part_number != \'-\'' );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'master_data_sparepart' );
    }
};
