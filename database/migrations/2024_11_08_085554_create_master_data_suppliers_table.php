<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'master_data_supplier', function (Blueprint $table)
        {
            $table->id (); // PK | id: Integer
            $table->string ( 'nama' ); // + nama: String
            $table->string ( 'alamat' ); // + alamat: String
            $table->string ( 'contact_person' ); // + contact_person: String

            $table->timestamps (); // + timestamps()
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'master_data_supplier' );
    }
};
