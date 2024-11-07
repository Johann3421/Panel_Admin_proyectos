<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita1; // Cambiado a Visita1

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
        $query = Visita1::query(); // Usar Visita1

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

        // Paginación
        $visitas = $query->paginate($limite);

        return view('reporte', compact('visitas', 'busqueda', 'fechaDesde', 'fechaHasta')); // Cambiado a 'reporte'
    }
}
