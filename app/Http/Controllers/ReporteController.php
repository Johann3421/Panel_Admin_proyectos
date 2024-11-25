<?php

namespace App\Http\Controllers;

use App\Exports\VisitaExport;
use Illuminate\Http\Request;
use App\Models\Visita1;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index(Request $request)
{
    // Parámetros de búsqueda y filtros de fecha
    $busqueda = $request->input('busqueda', '');
    $fechaDesde = $request->input('desde', '');
    $fechaHasta = $request->input('hasta', '');

    // Configuración de paginación
    $limite = 10;

    // Consulta para búsquedas y filtros
    $query = Visita1::query();

    if (!empty($busqueda)) {
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'LIKE', "%$busqueda%")
                ->orWhere('dni', 'LIKE', "%$busqueda%")
                ->orWhere('smotivo', 'LIKE', "%$busqueda%")
                ->orWhere('lugar', 'LIKE', "%$busqueda%");
        });
    }

    // Filtro por rango de fechas
    if (!empty($fechaDesde) && !empty($fechaHasta)) {
        $query->whereBetween('fecha', [$fechaDesde, $fechaHasta]);
    }

    // Ordenar por `hora_ingreso` (siempre que exista), y luego por `fecha` en orden descendente
    $query->orderByRaw('IFNULL(hora_ingreso, fecha) DESC');

    // Paginación
    $visitas = $query->paginate($limite);

    return view('reporte', compact('visitas', 'busqueda', 'fechaDesde', 'fechaHasta'));
}
    public function export(Request $request)
    {
        $busqueda = $request->input('busqueda', '');
        $fecha = $request->input('fecha', '');

        return Excel::download(new VisitaExport($busqueda, $fecha), 'visitas.xlsx');
    }
    public function vaciarFrontend()
{
    // En lugar de obtener visitas de la base de datos, devuelve una colección vacía
    $visitas = collect();

    return view('reporte', compact('visitas'))->with([
        'busqueda' => '',
        'fechaDesde' => '',
        'fechaHasta' => '',
        'success' => 'El frontend ha sido vaciado correctamente.',
    ]);
}


}
