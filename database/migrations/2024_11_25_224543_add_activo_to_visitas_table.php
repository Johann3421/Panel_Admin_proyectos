<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivoToVisitasTable extends Migration
{
    public function up()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('lugar');
        });
    }

    public function down()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
}
