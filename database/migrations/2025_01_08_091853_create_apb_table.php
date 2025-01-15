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
        Schema::create ( 'apb', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'tipe' );
            $table->date ( 'tanggal' );
            // $table->enum (
            //     'root_cause',
            //     [ 
            //         'pemeliharaan',
            //         'repair',
            //         'rusak',
            //         'tambah',
            //         'tersumbat'
            //     ]
            // );
            $table->string ( 'mekanik' )->nullable ();
            $table->integer ( 'quantity' );
            $table->enum (
                'status',
                [ 
                    'pending',
                    'accepted',
                    'rejected',
                ]
            )->nullable ()->default ( null );

            // Foreign keys sesuai dengan diagram
            $table->foreignId ( 'id_saldo' )
                ->nullable ()
                ->constrained ( 'saldo' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_proyek' )->nullable ()->constrained ( 'proyek' )->nullOnDelete ();
            $table->foreignId ( 'id_tujuan_proyek' )->nullable ()->constrained ( 'proyek' )->nullOnDelete ();
            $table->foreignId ( 'id_master_data_sparepart' )->nullable ()->constrained ( 'master_data_sparepart' )->nullOnDelete ();
            $table->foreignId ( 'id_master_data_supplier' )->nullable ()->constrained ( 'master_data_supplier' )->nullOnDelete ();
            $table->foreignId ( 'id_alat_proyek' )->nullable ()->constrained ( 'alat_proyek' )->nullOnDelete ();

            $table->timestamps ();
        } );

        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->foreign ( 'id_apb_mutasi' )
                ->references ( 'id' )
                ->on ( 'apb' )
                ->nullOnDelete ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'apb' );
    }
};
