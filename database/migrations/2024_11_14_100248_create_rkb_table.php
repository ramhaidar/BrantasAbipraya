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
        Schema::create ( 'rkb', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'nomor' );
            $table->date ( 'periode' ); // Format mm-yyyy dapat diatur di aplikasi

            $table->foreignId ( 'id_proyek' )->nullable ()->constrained ( 'proyek' )->nullOnDelete ();

            $table->boolean ( 'is_finalized' )->default ( false );

            $table->timestamps ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'rkb' );
    }
};
