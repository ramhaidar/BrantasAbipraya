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
        Schema::create ( 'lampiran_rkb_urgent', function (Blueprint $table)
        {
            $table->id ();

            $table->string ( 'dokumentasi' )->nullable ();

            $table->foreignId ( 'id_link_alat_detail_rkb' )
                ->nullable ()
                ->constrained ( 'link_alat_detail_rkb' )
                ->cascadeOnDelete ();

            $table->timestamps ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'lampiran_rkb_urgent' );
    }
};
