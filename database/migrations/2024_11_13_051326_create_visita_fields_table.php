<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('visita_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');         // Etiqueta del campo
            $table->string('name');          // Nombre del campo
            $table->string('type');          // Tipo de campo (text, textarea, select, radio, etc.)
            $table->json('options')->nullable();  // Opciones para select o radio
            $table->boolean('required')->default(false); // Campo requerido
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visita_fields');
    }
};
