<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'link_rkb_spb', function (Blueprint $table)
        {
            $table->id ();
            $table->foreignId ( 'id_rkb' )->constrained ( 'rkb' )->cascadeOnDelete ();
            $table->foreignId ( 'id_spb' )->constrained ( 'spb' )->cascadeOnDelete ();
            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'link_rkb_spb' );
    }
};
