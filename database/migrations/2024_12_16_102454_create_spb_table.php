<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'spb', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'nomor' );
            $table->date ( 'tanggal' );
            $table->foreignId ( 'id_master_data_supplier' )
                ->nullable ()
                ->constrained ( 'master_data_supplier' )
                ->nullOnDelete ();
            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'spb' );
    }
};
