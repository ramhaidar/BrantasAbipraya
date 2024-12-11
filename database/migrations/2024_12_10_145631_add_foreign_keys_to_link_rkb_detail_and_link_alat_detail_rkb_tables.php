<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::table ( 'link_rkb_detail', function (Blueprint $table)
        {
            $table->foreign ( 'id_link_alat_detail_rkb' )
                ->references ( 'id' )->on ( 'link_alat_detail_rkb' )
                ->cascadeOnDelete ();

            $table->foreign ( 'id_detail_rkb_general' )
                ->references ( 'id' )->on ( 'detail_rkb_general' )
                ->nullOnDelete ();

            $table->foreign ( 'id_detail_rkb_urgent' )
                ->references ( 'id' )->on ( 'detail_rkb_urgent' )
                ->nullOnDelete ();
        } );

        Schema::table ( 'link_alat_detail_rkb', function (Blueprint $table)
        {
            $table->foreign ( 'id_rkb' )
                ->references ( 'id' )->on ( 'rkb' )
                ->nullOnDelete ();

            $table->foreign ( 'id_master_data_alat' )
                ->references ( 'id' )->on ( 'master_data_alat' )
                ->nullOnDelete ();

            $table->foreign ( 'id_lampiran_rkb_urgent' )
                ->references ( 'id' )->on ( 'lampiran_rkb_urgent' )
                ->nullOnDelete ();
        } );
    }

    public function down () : void
    {
        Schema::table ( 'link_rkb_detail', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_link_alat_detail_rkb' ] );
            $table->dropForeign ( [ 'id_detail_rkb_general' ] );
            $table->dropForeign ( [ 'id_detail_rkb_urgent' ] );
        } );

        Schema::table ( 'link_alat_detail_rkb', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_rkb' ] );
            $table->dropForeign ( [ 'id_master_data_alat' ] );
        } );
    }
};
