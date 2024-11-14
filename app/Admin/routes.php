<?php

use App\Admin\Controllers\ModificadorVisitasController;
use Illuminate\Routing\Router;
use OpenAdmin\Admin\Facades\Admin;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),       // Prefijo del grupo de rutas para admin
    'namespace'     => config('admin.route.namespace'),    // Namespace para los controladores de admin
    'middleware'    => config('admin.route.middleware'),   // Middleware del grupo
    'as'            => config('admin.route.prefix') . '.', // Alias de las rutas
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('home');

    // Rutas para los controladores de administración
    $router->resource('visitas', 'VisitasAdminController');
    $router->resource('recesos', 'RecesosAdminController');
    $router->resource('modificador-visitas', ModificadorVisitasController::class);
});
