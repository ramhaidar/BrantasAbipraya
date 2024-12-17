<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'detail_rkb_general', function (Blueprint $table)
        {
            $table->id (); // PK
            $table->bigInteger ( 'quantity_requested' )->nullable (); // Jumlah diminta
            $table->bigInteger ( 'quantity_approved' )->nullable (); // Jumlah disetujui
            $table->bigInteger ( 'quantity_remainder' )->nullable (); // Jumlah sisa PO
            $table->string ( 'satuan' )->nullable (); // Satuan barang

            $table->foreignId ( 'id_kategori_sparepart_sparepart' )->nullable ()
                ->constrained ( 'kategori_sparepart' )->nullOnDelete (); // Kategori sparepart

            $table->foreignId ( 'id_master_data_sparepart' )->nullable ()
                ->constrained ( 'master_data_sparepart' )->nullOnDelete (); // Sparepart

            $table->timestamps (); // Timestamp pencatatan
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'detail_rkb_general' );
    }
};
