<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrabajadoresTable extends Migration
{
    public function up()
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id(); // Por defecto, es unsignedBigInteger en Laravel
            $table->string('nombre', 100);
            $table->string('dni', 8)->unique();
            $table->time('hora_receso')->nullable();
            $table->time('hora_vuelta')->nullable();
            $table->integer('duracion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trabajadores');
    }
}
