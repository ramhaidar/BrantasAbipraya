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

        // Add cascading deletes for related tables
        // Schema::table ( 'link_spb_detail_spb', function (Blueprint $table)
        // {
        //     $table->foreignId ( 'id_spb' )->constrained ( 'spb' )->cascadeOnDelete ();
        // } );

        // Schema::table ( 'detail_spb', function (Blueprint $table)
        // {
        //     $table->foreignId ( 'id_spb' )->constrained ( 'spb' )->cascadeOnDelete ();
        // } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        // Schema::table ( 'link_spb_detail_spb', function (Blueprint $table)
        // {
        //     $table->dropForeign ( [ 'id_spb' ] );
        // } );

        // Schema::table ( 'detail_spb', function (Blueprint $table)
        // {
        //     $table->dropForeign ( [ 'id_spb' ] );
        // } );
    }
};
