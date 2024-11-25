<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'link_supplier_sparepart', function (Blueprint $table)
        {
            $table->id ();
            $table->foreignId ( 'id_master_data_supplier' )->constrained ( 'master_data_supplier' )->cascadeOnDelete ();
            $table->foreignId ( 'id_master_data_sparepart' )->constrained ( 'master_data_sparepart' )->cascadeOnDelete ();

            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'link_supplier_sparepart' );
    }
};
