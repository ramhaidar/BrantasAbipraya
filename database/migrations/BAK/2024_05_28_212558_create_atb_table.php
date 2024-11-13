<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'atb', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'tipe' );
            $table->date ( 'tanggal' );
            $table->string ( 'dokumentasi' )->nullable ();
            $table->bigInteger ( 'quantity' );
            $table->string ( 'satuan' );
            $table->bigInteger ( 'harga' );
            $table->bigInteger ( 'net' );
            $table->bigInteger ( 'ppn' )->nullable ();
            $table->bigInteger ( 'bruto' )->nullable ();
            $table->foreignId ( 'id_komponen' )->nullable ()->nullOnDelete ();
            $table->foreignId ( 'id_saldo' )->nullable ()->unique ()->nullOnDelete ();
            $table->foreignId ( 'id_proyek' )->nullable ()->nullOnDelete ();
            $table->foreignId ( 'id_master_data' )->nullable ()->nullOnDelete ();
            $table->foreignId ( 'id_asal_proyek' )->nullable ()->nullOnDelete ();
            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'atb' );
    }
};
