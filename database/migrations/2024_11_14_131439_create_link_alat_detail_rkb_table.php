<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'link_alat_detail_rkb', function (Blueprint $table)
        {
            $table->id (); // PK

            $table->foreignId ( 'id_rkb' )->nullable ()
                ->constrained ( 'rkb' )
                ->nullOnDelete (); // Relasi ke RKB

            $table->foreignId ( 'id_master_data_alat' )->nullable ()
                ->constrained ( 'master_data_alat' )
                ->nullOnDelete (); // Relasi ke alat

            $table->foreignId ( 'id_timeline_rkb_urgent' )->nullable ()
                ->constrained ( 'timeline_rkb_urgent' )
                ->nullOnDelete (); // Relasi ke TimelineRKBUrgent

            $table->timestamps (); // Timestamp pencatatan
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'link_alat_detail_rkb' );
    }
};
