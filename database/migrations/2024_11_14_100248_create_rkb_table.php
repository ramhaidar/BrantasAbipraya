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
            $table->boolean ( 'is_approved' )->default ( false ); // Status persetujuan

            $table->integer ( 'harga' )->nullable (); // Harga sebelum pajak

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
