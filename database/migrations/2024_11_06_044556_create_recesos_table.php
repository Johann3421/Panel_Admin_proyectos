<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRecesosTable extends Migration
{
    public function up()
    {
        Schema::create('recesos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trabajador_id'); // Debe coincidir como unsignedBigInteger
            $table->string('nombre', 100)->nullable();
            $table->string('dni', 20)->nullable();
            $table->integer('duracion')->nullable();
            $table->dateTime('hora_receso')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->time('hora_vuelta')->nullable();
            $table->enum('estado', ['activo', 'finalizado'])->default('activo');
            $table->integer('exceso')->default(0);
            $table->timestamps();

            
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitas');
    }
}
