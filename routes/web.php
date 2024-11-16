<?php

use App\Admin\Controllers\ModificadorRecesosController;
use App\Admin\Controllers\VisitasAdminController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CronometroController;
use App\Http\Controllers\DniController;
use App\Http\Controllers\ExportarExcelController;
use App\Http\Controllers\FiltroVisitaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\VisitaController;
use Illuminate\Support\Facades\Route;


// Redirige la raíz a la página de login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');

// Recurso para visitas, crea rutas como visitas.index, visitas.create, etc.
Route::resource('visitas', VisitaController::class);

Route::post('/visitas/{id}/salida', [VisitaController::class, 'registrarSalida'])->name('visitas.registrarSalida');

// Rutas de reporte
Route::get('/reporte', [ReporteController::class, 'index'])->name('reporte.index');
Route::get('/reporte/export', [ReporteController::class, 'export'])->name('reporte.export');

// Rutas de cronómetro
Route::get('/cronometro', [CronometroController::class, 'index'])->name('cronometro.index');
Route::post('/registrar-receso', [CronometroController::class, 'registrarReceso'])->name('cronometro.registrar');
Route::post('/finalizar-receso', [CronometroController::class, 'finalizarReceso'])->name('cronometro.finalizar');
Route::get('/tiempos-restantes', [CronometroController::class, 'tiemposRestantes'])->name('cronometro.tiemposRestantes');
Route::get('/cronometro/buscar-trabajador', [CronometroController::class, 'buscarTrabajador'])->name('cronometro.buscarTrabajador');

// Rutas de recesos
Route::get('/recesos', [App\Http\Controllers\RecesoController::class, 'index'])->name('recesos.index');
Route::get('/recesos/export', [App\Http\Controllers\RecesoController::class, 'export'])->name('recesos.export');

// Ruta de ticket de visita
Route::get('/visitas/{id}/imprimir-ticket', [ExportarExcelController::class, 'imprimirTicket'])->name('visitas.imprimirTicket');

// Ruta para búsqueda de DNI
Route::post('/buscar-dni', [DniController::class, 'buscarDni'])->name('buscar.dni');

// Ruta de filtro de visitas (con middleware de autenticación)
Route::middleware('auth:sanctum')->get('/filtro-visitas', [FiltroVisitaController::class, 'filtrar']);

Route::get('/admin/visitas/estadisticas', [VisitasAdminController::class, 'estadisticas']);
Route::resource('receso_fields', ModificadorRecesosController::class);

Route::match(['get', 'post'], '/botman', [ChatbotController::class, 'handle']);