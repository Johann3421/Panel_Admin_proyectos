<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;

class FiltroVisitaController extends Controller
{
    public function filtrar(Request $request)
    {
        // Verificación de autenticación
        if (!auth()->check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Configuración de la paginación
        $limite = 10; // Registros por página
        $pagina = $request->input('pagina', 1);

        // Parámetros de filtro y búsqueda
        $fecha = $request->input('fecha');
        $busqueda = $request->input('busqueda');

        // Construir la consulta base para registros sin hora de salida
        $query = Visita::whereNull('hora_salida')
                        ->orWhere('hora_salida', '');

        // Filtrar por fecha
        if ($fecha) {
            $query->where('fecha', $fecha);
        }

        // Filtrar por búsqueda
        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('dni', 'like', "%{$busqueda}%")
                  ->orWhere('smotivo', 'like', "%{$busqueda}%")
                  ->orWhere('lugar', 'like', "%{$busqueda}%");
            });
        }

        // Aplicar paginación
        $visitas = $query->paginate($limite, ['*'], 'page', $pagina);

        return response()->json([
            'data' => $visitas->items(),
            'total_filas' => $visitas->total(),
            'total_paginas' => $visitas->lastPage(),
            'pagina_actual' => $visitas->currentPage(),
        ]);
    }
}
