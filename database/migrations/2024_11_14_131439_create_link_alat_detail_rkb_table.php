<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'link_alat_detail_rkb', function (Blueprint $table)
        {
            $table->id (); // PK
            $table->string ( 'nama_koordinator' )->nullable ();

            $table->unsignedBigInteger ( 'id_rkb' )->nullable ();
            $table->unsignedBigInteger ( 'id_master_data_alat' )->nullable ();
            $table->unsignedBigInteger ( 'id_lampiran_rkb_urgent' )->nullable ();

            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'link_alat_detail_rkb' );
    }
};
