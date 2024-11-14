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
        Schema::create ( 'link_rkb_detail', function (Blueprint $table)
        {
            $table->id ();
            $table->timestamps ();

            $table->foreignId ( 'id_rkb' )->nullable ()->constrained ( 'rkb' )->cascadeOnDelete ();
            $table->foreignId ( 'id_detail_rkb_general' )->nullable ()->constrained ( 'detail_rkb_general' )->nullOnDelete ();
            $table->foreignId ( 'id_detail_rkb_urgent' )->nullable ()->constrained ( 'detail_rkb_urgent' )->nullOnDelete ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'link_rkb_detail' );
    }
};
