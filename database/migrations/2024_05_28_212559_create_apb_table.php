<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up () : void
{
Schema::create ( 'apb', function (Blueprint $table)
{
$table->id ();
$table->date ( 'tanggal' );
$table->bigInteger ( 'quantity' )->nullable ();
$table->string ( 'dokumentasi' )->nullable ();
$table->foreignId ( 'id_alat' )->nullable ()->onDelete ( 'set null' );
$table->foreignId ( 'id_saldo' )->nullable ()->onDelete ( 'set null' );
$table->foreignId ( 'id_tujuan_proyek' )->nullable ()->onDelete ( 'set null' );
$table->timestamps ();
} );
}

public function down () : void
{
Schema::dropIfExists ( 'apb' );
}
};
