<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'apb', function (Blueprint $table)
        {
            $table->id ();
            $table->date ( 'tanggal' );
            $table->bigInteger ( 'quantity' )->nullable ();
            $table->string ( 'dokumentasi' )->nullable ();
            $table->foreignId ( 'id_master_data_alat' )->nullable ()->nullOnDelete ();
            $table->foreignId ( 'id_saldo' )->nullable ()->nullOnDelete ();
            $table->foreignId ( 'id_tujuan_proyek' )->nullable ()->nullOnDelete ();
            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'apb' );
    }
};
