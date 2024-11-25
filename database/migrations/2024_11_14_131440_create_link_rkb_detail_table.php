<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'link_rkb_detail', function (Blueprint $table)
        {
            $table->id (); // PK

            $table->foreignId ( 'id_link_alat_detail_rkb' )->nullable ()
                ->constrained ( 'link_alat_detail_rkb' )
                ->cascadeOnDelete (); // Relasi ke tabel LinkAlatDetailRKB

            $table->foreignId ( 'id_detail_rkb_general' )->nullable ()
                ->constrained ( 'detail_rkb_general' )
                ->nullOnDelete (); // Relasi ke DetailRKBGeneral

            $table->foreignId ( 'id_detail_rkb_urgent' )->nullable ()
                ->constrained ( 'detail_rkb_urgent' )
                ->nullOnDelete (); // Relasi ke DetailRKBUrgent

            $table->timestamps (); // Timestamp pencatatan
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'link_rkb_detail' );
    }
};
