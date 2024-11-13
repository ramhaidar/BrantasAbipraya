<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'saldo', function (Blueprint $table)
        {
            $table->id ();
            $table->bigInteger ( 'current_quantity' )->nullable ();
            $table->bigInteger ( 'net' );
            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'saldo' );
    }
};
