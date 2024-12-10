<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'timeline_rkb_urgent', function (Blueprint $table)
        {
            $table->id (); // PK

            $table->string ( 'nama_rencana' ); // Nama rencana
            $table->date ( 'tanggal_awal_rencana' ); // Tanggal awal rencana
            $table->date ( 'tanggal_akhir_rencana' ); // Tanggal akhir rencana
            $table->date ( 'tanggal_awal_actual' )->nullable (); // Tanggal awal aktual
            $table->date ( 'tanggal_akhir_actual' )->nullable (); // Tanggal akhir aktual
            $table->boolean ( 'is_done' )->default ( false ); // Status selesai

            $table->foreignId ( 'id_link_alat_detail_rkb' )
                ->nullable ()
                ->constrained ( 'link_alat_detail_rkb' )
                ->cascadeOnDelete (); // FK ke tabel LinkAlatDetailRKB

            $table->timestamps (); // Timestamps
        } );

    }

    public function down () : void
    {
        Schema::dropIfExists ( 'timeline_rkb_urgent' );
    }
};
