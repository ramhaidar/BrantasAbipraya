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
        Schema::create ( 'detail_spb', function (Blueprint $table)
        {
            $table->id ();
            $table->bigInteger ( 'quantity_po' );
            $table->bigInteger ( 'quantity_belum_diterima' )->default ( 0 );
            $table->string ( 'satuan' );
            $table->bigInteger ( 'harga' );
            $table->foreignId ( 'id_master_data_sparepart' )
                ->constrained ( 'master_data_sparepart' )->cascadeOnDelete ();
            $table->foreignId ( 'id_master_data_alat' )
                ->constrained ( 'master_data_alat' )->cascadeOnDelete ();
            $table->foreignId ( 'id_link_rkb_detail' )
                ->constrained ( 'link_rkb_detail' )->cascadeOnDelete ();

            $table->timestamps ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'detail_spb' );
    }
};
