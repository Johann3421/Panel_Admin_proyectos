<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * Define los comandos de la aplicación.
     */
    protected $commands = [
        // Añadir comandos personalizados aquí si los necesitas
    ];

    /**
     * Programa las tareas recurrentes.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            // Llama al método para vaciar el frontend
            Http::get(route('reporte.vaciar'));
        })->monthlyOn(1, '00:00'); // Ejecuta el primer día de cada mes a medianoche
    }

    /**
     * Registra los comandos para ejecutar con Artisan.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
