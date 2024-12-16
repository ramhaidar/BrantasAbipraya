<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spb', function (Blueprint $table) {
            $table->id(); // PK
            $table->string('nomor');
            $table->timestamps();
        });

        Schema::table('rkb', function (Blueprint $table) {
            $table->foreign('id_spb')->references('id')->on('spb')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rkb', function (Blueprint $table) {
            $table->dropForeign(['id_spb']);
        });

        Schema::dropIfExists('spb');
    }
};
