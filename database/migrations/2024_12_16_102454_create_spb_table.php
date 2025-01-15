<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'spb', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'nomor' );
            $table->boolean ( 'is_addendum' )->default ( false );
            $table->date ( 'tanggal' );
            $table->foreignId ( 'id_master_data_supplier' )
                ->nullable ()
                ->constrained ( 'master_data_supplier' )
                ->nullOnDelete ();
            $table->foreignId ( 'id_spb_original' )
                ->nullable ()
                ->constrained ( 'spb' )
                ->nullOnDelete ();

            $table->timestamps ();
        } );

        // Add cascading deletes for related tables
        // Schema::table('link_spb_detail_spb', function (Blueprint $table) {
        //     $table->foreignId('id_spb')->constrained('spb')->cascadeOnDelete();
        // });

        // Schema::table('detail_spb', function (Blueprint $table) {
        //     $table->foreignId('id_spb')->constrained('spb')->cascadeOnDelete();
        // });
    }

    public function down () : void
    {
        // Schema::table('link_spb_detail_spb', function (Blueprint $table) {
        //     $table->dropForeign(['id_spb']);
        // });

        // Schema::table('detail_spb', function (Blueprint $table) {
        //     $table->dropForeign(['id_spb']);
        // });

        Schema::dropIfExists ( 'spb' );
    }
};
