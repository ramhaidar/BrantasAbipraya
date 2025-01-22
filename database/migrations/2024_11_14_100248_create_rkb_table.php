<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'rkb', function (Blueprint $table)
        {
            $table->id (); // PK
            $table->string ( 'nomor' ); // Nomor unik RKB
            $table->date ( 'periode' ); // Format mm-yyyy dapat diatur di aplikasi

            $table->enum (
                'tipe',
                [ 'general', 'urgent' ]
            )->default ( 'general' );


            $table->boolean ( 'is_finalized' )->default ( false ); // Status finalisasi
            $table->boolean ( 'is_evaluated' )->default ( false ); // Status evaluasi

            // Replace single approval with two stages
            $table->boolean ( 'is_approved_vp' )->default ( false ); // Approval by VP
            $table->boolean ( 'is_approved_svp' )->default ( false ); // Approval by SVP

            // Optional: Add timestamp columns for tracking when each approval occurred
            $table->timestamp ( 'vp_approved_at' )->nullable ();
            $table->timestamp ( 'svp_approved_at' )->nullable ();

            $table->foreignId ( 'id_proyek' )->nullable ()
                ->constrained ( 'proyek' )->nullOnDelete (); // Relasi ke tabel proyek

            $table->timestamps (); // Timestamp pencatatan
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'rkb' );
    }
};
