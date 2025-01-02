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
            $table->string ( 'tipe' );
            $table->string ( 'dokumentasi_foto' ); // Folder path sesuai dengan diagram
            $table->string ( 'surat_tanda_terima' ); // File path sesuai dengan diagram
            $table->date ( 'tanggal' );
            $table->bigInteger ( 'quantity' );
            $table->bigInteger ( 'harga' );
            $table->timestamps ();

            // Foreign keys sesuai dengan diagram
            $table->foreignId ( 'id_proyek' )->nullable ()->constrained ( 'proyek' )->nullOnDelete ();
            $table->foreignId ( 'id_asal_proyek' )->nullable ()->constrained ( 'proyek' )->nullOnDelete ();
            $table->foreignId ( 'id_spb' )->nullable ()->constrained ( 'spb' )->nullOnDelete ();
            $table->foreignId ( 'id_detail_spb' )->nullable ()->constrained ( 'detail_spb' )->nullOnDelete ();
            $table->foreignId ( 'id_master_data_sparepart' )->nullable ()->constrained ( 'master_data_sparepart' )->nullOnDelete ();
            $table->foreignId ( 'id_master_data_supplier' )->nullable ()->constrained ( 'master_data_supplier' )->nullOnDelete ();
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
