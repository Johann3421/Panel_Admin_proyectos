<?php

use App\Http\Controllers\CronometroController;
use App\Http\Controllers\DniController;
use App\Http\Controllers\ExportarExcelController;
use App\Http\Controllers\FiltroVisitaController;
use App\Http\Controllers\RecesoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VisitaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('visitas', VisitaController::class);
Route::post('/visitas/{id}/salida', [VisitaController::class, 'registrarSalida'])->name('visitas.registrarSalida');

// Rutas de reporte
Route::get('/reporte', [ReporteController::class, 'index'])->name('reporte.index');
Route::get('/reporte/export', [ReporteController::class, 'export'])->name('reporte.export'); // Definición correcta de reporte.export

Route::get('/cronometro', [CronometroController::class, 'index'])->name('cronometro.index');
Route::post('/registrar-receso', [CronometroController::class, 'registrarReceso'])->name('cronometro.registrar');
Route::post('/finalizar-receso', [CronometroController::class, 'finalizarReceso'])->name('cronometro.finalizar');
Route::get('/tiempos-restantes', [CronometroController::class, 'tiemposRestantes'])->name('cronometro.tiemposRestantes');
Route::get('/cronometro/buscar-trabajador', [CronometroController::class, 'buscarTrabajador'])->name('cronometro.buscarTrabajador');

Route::get('/recesos', [App\Http\Controllers\RecesoController::class, 'index'])->name('recesos.index');
Route::get('/recesos/export', [App\Http\Controllers\RecesoController::class, 'export'])->name('recesos.export');


Route::get('/visitas/{id}/imprimir-ticket', [ExportarExcelController::class, 'imprimirTicket'])->name('visitas.imprimirTicket');

// Ruta para búsqueda de DNI
Route::post('/buscar-dni', [DniController::class, 'buscarDni'])->name('buscar.dni');
Route::middleware('auth:sanctum')->get('/filtro-visitas', [FiltroVisitaController::class, 'filtrar']);
