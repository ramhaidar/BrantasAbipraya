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
        Schema::create ( 'alat_proyek', function (Blueprint $table)
        {
            $table->id ();
            $table->timestamp ( 'assigned_at' )->useCurrent ();
            $table->timestamp ( 'removed_at' )->nullable ();
            $table->foreignId ( 'id_master_data_alat' )->constrained ( 'master_data_alat' )->cascadeOnDelete ();
            $table->foreignId ( 'id_proyek' )->constrained ( 'proyek' )->cascadeOnDelete ();

            $table->timestamps ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'alat_proyek' );
    }
};
