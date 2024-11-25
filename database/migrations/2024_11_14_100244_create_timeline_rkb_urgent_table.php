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
            $table->date ( 'tanggal_awal_actual' )->nullable (); // Tanggal aktual mulai
            $table->date ( 'tanggal_akhir_actual' )->nullable (); // Tanggal aktual selesai
            $table->boolean ( 'is_done' )->default ( false ); // Status selesai

            $table->foreignId ( 'id_kategori_sparepart_sparepart' )->nullable ()
                ->constrained ( 'kategori_sparepart' )
                ->nullOnDelete (); // FK kategori sparepart
            $table->foreignId ( 'id_master_data_sparepart' )->nullable ()
                ->constrained ( 'master_data_sparepart' )
                ->nullOnDelete (); // FK master data sparepart

            $table->timestamps (); // Timestamp pencatatan
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'timeline_rkb_urgent' );
    }
};
