<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::table ( 'master_data_supplier', function (Blueprint $table)
        {
            $table->unique ( 'nama' );
        } );
    }

    public function down () : void
    {
        Schema::table ( 'master_data_supplier', function (Blueprint $table)
        {
            $table->dropUnique ( [ 'nama' ] );
        } );
    }
};
