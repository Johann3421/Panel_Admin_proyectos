<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivoToRecesosTable extends Migration
{
    public function up()
    {
        Schema::table('recesos', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('estado');
        });
    }

    public function down()
    {
        Schema::table('recesos', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
}
