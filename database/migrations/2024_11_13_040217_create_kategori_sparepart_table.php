<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up ()
    {
        Schema::create ( 'kategori_sparepart', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'kode' )->unique ();
            $table->string ( 'nama' );
            $table->string ( 'jenis' )->nullable ();      // Add jenis column
            $table->string ( 'sub_jenis' )->nullable ();   // Add sub_jenis column
            $table->timestamps ();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::dropIfExists ( 'kategori_sparepart' );
    }
};
