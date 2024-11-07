<?php

use App\Http\Controllers\CronometroController;
use App\Http\Controllers\ExportarExcelController;
use App\Http\Controllers\RecesoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VisitaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('visitas', VisitaController::class);
Route::post('buscar-dni', [VisitaController::class, 'buscarDNI'])->name('visitas.buscarDNI');
Route::get('/reporte', [ReporteController::class, 'index'])->name('reporte.index');
Route::get('/cronometro', [CronometroController::class, 'index'])->name('cronometro.index');
Route::post('/cronometro/finalizar/{id}', [CronometroController::class, 'finalizarReceso'])->name('cronometro.finalizar');
Route::get('/recesos', [RecesoController::class, 'index'])->name('recesos.index');
Route::get('/recesos/export', [RecesoController::class, 'export'])->name('recesos.export');
Route::get('/exportar-excel', [ExportarExcelController::class, 'exportar'])->name('exportar.excel');