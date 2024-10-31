<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'komponen', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'kode' );
            $table->foreignId ( 'first_group_id' )->nullable ()->constrained ( 'first_group' )->cascadeOnDelete ();
            $table->foreignId ( 'second_group_id' )->nullable ()->constrained ( 'second_group' )->cascadeOnDelete ();
            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'komponen' );
    }
};
