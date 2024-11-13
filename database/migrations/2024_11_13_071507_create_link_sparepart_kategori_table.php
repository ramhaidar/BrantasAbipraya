<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'link_sparepart_kategori', function (Blueprint $table)
        {
            $table->id ();
            $table->foreignId ( 'id_sparepart' )->constrained ( 'master_data_spareparts' )->cascadeOnDelete ();
            $table->foreignId ( 'id_kategori' )->constrained ( 'kategori_sparepart' )->cascadeOnDelete ();
            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'link_sparepart_kategori' );
    }

};
