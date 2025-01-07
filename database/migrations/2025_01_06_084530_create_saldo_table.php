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
            $table->bigInteger ( 'quantity' );
            $table->bigInteger ( 'harga' );
            $table->timestamps ();

            // Foreign keys
            $table->foreignId ( 'id_proyek' )
                ->constrained ( 'proyek' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_asal_proyek' )
                ->constrained ( 'proyek' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_spb' )
                ->constrained ( 'spb' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_master_data_sparepart' )
                ->unique ()
                ->constrained ( 'master_data_sparepart' )
                ->onDelete ( 'cascade' );
            $table->foreignId ( 'id_master_data_supplier' )
                ->constrained ( 'master_data_supplier' )
                ->onDelete ( 'cascade' );
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
