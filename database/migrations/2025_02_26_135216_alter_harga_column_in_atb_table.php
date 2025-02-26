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
        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->decimal ( 'harga', 18, 5 )->change ();
        } );

        Schema::table ( 'detail_spb', function (Blueprint $table)
        {
            $table->decimal ( 'harga', 18, 5 )->change ();
        } );

        Schema::table ( 'saldo', function (Blueprint $table)
        {
            $table->decimal ( 'harga', 18, 5 )->change ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->bigInteger ( 'harga' )->change ();
        } );

        Schema::table ( 'detail_spb', function (Blueprint $table)
        {
            $table->bigInteger ( 'harga' )->change ();
        } );

        Schema::table ( 'saldo', function (Blueprint $table)
        {
            $table->bigInteger ( 'harga' )->change ();
        } );
    }
};
