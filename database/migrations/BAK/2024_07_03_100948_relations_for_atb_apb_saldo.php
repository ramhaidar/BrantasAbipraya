<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->foreign ( 'id_komponen' )->references ( 'id' )->on ( 'komponen' )->cascadeOnDelete ();
            $table->foreign ( 'id_saldo' )->references ( 'id' )->on ( 'saldo' )->cascadeOnDelete ();
        } );

        Schema::table ( 'apb', function (Blueprint $table)
        {
            $table->foreign ( 'id_master_data_alat' )->references ( 'id' )->on ( 'alat' )->cascadeOnDelete ();
            $table->foreign ( 'id_saldo' )->references ( 'id' )->on ( 'saldo' )->cascadeOnDelete ();
        } );
    }

    public function down () : void
    {
        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_komponen' ] );
            $table->dropForeign ( [ 'id_saldo' ] );
        } );

        Schema::table ( 'apb', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_master_data_alat' ] );
            $table->dropForeign ( [ 'id_saldo' ] );
        } );
    }
};
