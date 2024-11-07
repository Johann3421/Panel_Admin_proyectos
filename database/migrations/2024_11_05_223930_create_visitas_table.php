<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVisitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8);
            $table->string('nombre', 100);
            $table->string('tipopersona', 50)->nullable();
            $table->string('nomoficina', 100)->nullable();
            $table->string('smotivo', 100)->nullable();
            $table->string('lugar', 100)->nullable();
            $table->date('fecha')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->time('hora_ingreso')->nullable();
            $table->time('hora_salida')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitas');
    }
}
