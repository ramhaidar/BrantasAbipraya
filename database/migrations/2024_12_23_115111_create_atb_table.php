<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up () : void
    {
        Schema::create ( 'atb', function (Blueprint $table)
        {
            $table->id ();
            $table->enum ( 'tipe', [ 
                'hutang_unit_alat',
                'mutasi_proyek',
                'panjar_unit_alat',
                'panjar_proyek'
            ] );
            $table->date ( 'tanggal' );
            $table->string ( 'dokumentasi' );
            $table->bigInteger ( 'quantity' );
            $table->string ( 'satuan' );
            $table->bigInteger ( 'harga' );
            $table->foreignId ( 'id_spb' )->nullable ()->unique ()->nullOnDelete ();
            $table->foreignId ( 'id_saldo' )->nullable ()->unique ()->nullOnDelete ();
            $table->foreignId ( 'id_proyek' )->nullable ()->nullOnDelete ();
            $table->foreignId ( 'id_master_data_alat' )->nullable ()->nullOnDelete ();
            $table->foreignId ( 'id_asal_proyek' )->nullable ()->nullOnDelete ();
            $table->timestamps ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'atb' );
    }
};
