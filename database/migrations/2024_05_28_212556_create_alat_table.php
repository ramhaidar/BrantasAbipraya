<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'alat', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'nama_proyek' )->nullable ();
            $table->string ( 'jenis_alat' );
            $table->string ( 'kode_alat' );
            $table->string ( 'merek_alat' );
            $table->string ( 'tipe_alat' );

            $table->foreignId ( 'id_proyek' )->nullable ();
            $table->foreignId ( 'id_user' )->constrained ( 'users' )->onDelete ( 'cascade' );

            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'alat' );
    }
};
