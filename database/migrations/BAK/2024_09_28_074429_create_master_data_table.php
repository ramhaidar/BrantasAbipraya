<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up () : void
    {
        Schema::create ( 'master_data', function (Blueprint $table)
        {
            $table->id ();
            $table->string ( 'id_master_data_supplier' );
            $table->string ( 'sparepart' );
            $table->string ( 'part_number' );
            $table->integer ( 'buffer_stock' )->nullable ();
            ;
            $table->unsignedBigInteger ( 'id_user' )->nullable ();
            $table->foreign ( 'id_user' )->references ( 'id' )->on ( 'users' )->cascadeOnDelete ();
            $table->timestamps ();
        } );

        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->foreign ( 'id_master_data' )->references ( 'id' )->on ( 'master_data' )->cascadeOnDelete ();
        } );

    }

    /**
     * Reverse the migrations.
     */
    public function down () : void
    {
        Schema::table ( 'atb', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_master_data' ] );
        } );

        Schema::table ( 'master_data', function (Blueprint $table)
        {
            $table->dropForeign ( [ 'id_user' ] );
            $table->dropColumn ( 'id_user' );
        } );

        Schema::dropIfExists ( 'master_data' );
    }
};
