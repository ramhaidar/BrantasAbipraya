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
            $table->id (); // PK | id: Integer
            $table->timestamps (); // + timestamps()
            $table->foreignId ( 'id_supplier' )->constrained ( 'master_data_suppliers' ); // FK | id_supplier: ForeignID
            $table->foreignId ( 'id_sparepart' )->constrained ( 'master_data_spareparts' ); // FK | id_sparepart: ForeignID
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'link_supplier_sparepart' );
    }
};
