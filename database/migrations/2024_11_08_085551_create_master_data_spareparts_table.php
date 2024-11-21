<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'master_data_sparepart', function (Blueprint $table)
        {
            $table->id (); // PK | id: Integer
            $table->string ( 'nama' ); // + nama: String
            $table->string ( 'part_number' ); // + part_number: String
            $table->string ( 'merk' ); // + merk: String
            $table->foreignId ( 'id_kategori' )
                ->nullable () // Kolom harus nullable
                ->constrained ( 'kategori_sparepart' )
                ->nullOnDelete (); // Foreign key dengan ON DELETE SET NULL

            $table->timestamps (); // + timestamps()
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'master_data_sparepart' );
    }
};
