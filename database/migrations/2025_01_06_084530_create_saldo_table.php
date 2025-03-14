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
        Schema::create ( 'saldo', function (Blueprint $table)
        {
            $table->id (); // Primary Key (id)
            $table->string ( 'tipe' );
            $table->string ( 'satuan' );
            $table->bigInteger ( 'quantity' );
            $table->bigInteger ( 'harga' );

            // Foreign keys
            $table->foreignId ( 'id_atb' )
                ->unique ()
                ->constrained ( 'atb' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_proyek' )
                ->nullable ()
                ->constrained ( 'proyek' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_asal_proyek' )
                ->nullable ()
                ->constrained ( 'proyek' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_spb' )
                ->nullable ()
                ->constrained ( 'spb' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_master_data_sparepart' )
                ->nullable ()
                ->constrained ( 'master_data_sparepart' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_master_data_supplier' )
                ->nullable ()
                ->constrained ( 'master_data_supplier' )
                ->onDelete ( 'cascade' );

            $table->timestamps ();
        } );

    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'saldo' );
    }
};
