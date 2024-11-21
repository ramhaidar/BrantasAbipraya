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
        Schema::create ( 'detail_rkb_urgent', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'quantity' )->nullable ();
            $table->string ( 'satuan' )->nullable ();
            $table->string ( 'kronologi' )->nullable ();

            $table->foreignId ( 'id_alat' )->nullable ()->constrained ( 'master_data_alat' )->nullOnDelete ();
            $table->foreignId ( 'id_kategori_sparepart' )->nullable ()->constrained ( 'kategori_sparepart' )->nullOnDelete ();
            $table->foreignId ( 'id_sparepart' )->nullable ()->constrained ( 'master_data_sparepart' )->nullOnDelete ();

            $table->timestamps ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'detail_rkb_urgent' );
    }
};
