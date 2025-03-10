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

            // Unique constraint untuk kombinasi merk dan part_number
            // $table->unique ( [ 'merk', 'part_number' ] );

        } );
        // Nama dan Part Number boleh sama jika merk berbeda
        DB::statement ( 'CREATE UNIQUE INDEX unique_nama_part_number_except_merk ON master_data_sparepart (nama, part_number)' );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'master_data_sparepart' );
    }
};
