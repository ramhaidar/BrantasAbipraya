<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up () : void
    {
        Schema::create ( 'user_proyek', function (Blueprint $table)
        {
            $table->id ();
            $table->foreignId ( 'id_proyek' )->constrained ( 'proyek' )->cascadeOnDelete ();
            $table->foreignId ( 'id_user' )->constrained ( 'users' )->cascadeOnDelete ();

            $table->timestamps ();
        } );
    }

    public function down () : void
    {
        Schema::dropIfExists ( 'user_proyek' );
    }
};
