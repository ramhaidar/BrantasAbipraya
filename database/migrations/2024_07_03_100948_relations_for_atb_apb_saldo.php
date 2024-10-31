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
            $table->foreign ( 'id_komponen' )->references ( 'id' )->on ( 'komponen' )->onDelete ( 'cascade' );
            $table->foreign ( 'id_saldo' )->references ( 'id' )->on ( 'saldo' )->onDelete ( 'cascade' );
        } );

        Schema::table ( 'apb', function (Blueprint $table)
        {
            $table->foreign ( 'id_alat' )->references ( 'id' )->on ( 'alat' )->onDelete ( 'cascade' );
            $table->foreign ( 'id_saldo' )->references ( 'id' )->on ( 'saldo' )->onDelete ( 'cascade' );
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
            $table->dropForeign ( [ 'id_alat' ] );
            $table->dropForeign ( [ 'id_saldo' ] );
        } );
    }
};
