<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RecesosExport;

class RecesoController extends Controller
{
    public function index(Request $request)
{
    // Obtener filtros de búsqueda y fechas
    $busqueda = $request->input('busqueda');
    $desde = $request->input('desde');
    $hasta = $request->input('hasta');

    // Consultar la tabla recesos con filtros, incluyendo el campo 'exceso'
    $query = DB::table('recesos')->select(
        'id', 'trabajador_id', 'nombre', 'dni', 'hora_receso', 
        'hora_vuelta', 'duracion', 'estado', 'exceso'
    );

    if ($busqueda) {
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'like', '%' . $busqueda . '%')
              ->orWhere('dni', 'like', '%' . $busqueda . '%');
        });
    }

    if ($desde) {
        $query->whereDate('hora_receso', '>=', $desde);
    }

    if ($hasta) {
        $query->whereDate('hora_receso', '<=', $hasta);
    }

    $recesos = $query->paginate(10);

    return view('recesos', compact('recesos'));
}



    public function export(Request $request)
{
    // Proporcionar valores predeterminados para evitar el error de clave indefinida
    $filters = [
        'busqueda' => $request->input('busqueda', ''), // Valor predeterminado: cadena vacía
        'desde' => $request->input('desde', ''),       // Valor predeterminado: cadena vacía
        'hasta' => $request->input('hasta', '')        // Valor predeterminado: cadena vacía
    ];

    return Excel::download(new RecesosExport($filters), 'recesos.xlsx');
}

}
