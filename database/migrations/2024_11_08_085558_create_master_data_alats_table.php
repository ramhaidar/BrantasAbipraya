<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'master_data_alat', function (Blueprint $table)
        {
            $table->id (); // PK | id: Integer
            $table->string ( 'jenis_alat' ); // + jenis_alat: String
            $table->string ( 'kode_alat' ); // + kode_alat: String
            $table->string ( 'merek_alat' ); // + merek_alat: String
            $table->string ( 'tipe_alat' ); // + tipe_alat: String
            $table->string ( 'serial_number' ); // + serial_number: String

            $table->timestamps (); // + timestamps()
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'master_data_alat' );
    }
};
