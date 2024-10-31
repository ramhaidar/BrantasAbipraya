<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::table ( 'alat', function (Blueprint $table)
        {
            $table->foreign ( 'id_proyek' )->references ( 'id' )->on ( 'proyek' )->onDelete ( 'set null' );
        } );

        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->foreign ( 'id_proyek' )->references ( 'id' )->on ( 'proyek' )->onDelete ( 'cascade' );
            $table->foreign ( 'id_asal_proyek' )->references ( 'id' )->on ( 'proyek' )->onDelete ( 'cascade' );
        } );

        Schema::table ( 'apb', function (Blueprint $table)
        {
            $table->foreign ( 'id_tujuan_proyek' )->references ( 'id' )->on ( 'proyek' )->onDelete ( 'cascade' );
        } );

    }

    public function down () : void
    {
        Schema::table ( 'alat', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_proyek' ] );
            $table->dropColumn ( 'id_proyek' );

        } );

        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_proyek' ] );
            $table->dropColumn ( 'id_proyek' );
            $table->dropForeign ( [ 'id_asal_proyek' ] );
            $table->dropColumn ( 'id_asal_proyek' );
        } );

        Schema::table ( 'apb', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_tujuan_proyek' ] );
            $table->dropColumn ( 'id_tujuan_proyek' );
        } );

    }
};
