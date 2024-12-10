<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'link_rkb_detail', function (Blueprint $table)
        {
            $table->id (); // PK

            $table->unsignedBigInteger ( 'id_link_alat_detail_rkb' )->nullable ();
            $table->unsignedBigInteger ( 'id_detail_rkb_general' )->nullable ();
            $table->unsignedBigInteger ( 'id_detail_rkb_urgent' )->nullable ();

            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'link_rkb_detail' );
    }
};
