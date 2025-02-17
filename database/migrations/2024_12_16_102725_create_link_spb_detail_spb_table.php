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
        Schema::create ( 'link_spb_detail_spb', function (Blueprint $table)
        {
            $table->id ();
            $table->foreignId ( 'id_spb' )->constrained ( 'spb' )->cascadeOnDelete ();
            $table->foreignId ( 'id_detail_spb' )->constrained ( 'detail_spb' )->cascadeOnDelete ();

            $table->timestamps ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'link_spb_detail_spb' );
    }
};
