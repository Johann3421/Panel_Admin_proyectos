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
    $busqueda = $request->input('busqueda', '');
    $fechaDesde = $request->input('desde', '');
    $fechaHasta = $request->input('hasta', '');
    $limite = 10;

    $query = Visita1::where('activo', true); // Filtrar visitas activas

    if (!empty($busqueda)) {
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'LIKE', "%$busqueda%")
                ->orWhere('dni', 'LIKE', "%$busqueda%")
                ->orWhere('smotivo', 'LIKE', "%$busqueda%")
                ->orWhere('lugar', 'LIKE', "%$busqueda%");
        });
    }

    if (!empty($fechaDesde) && !empty($fechaHasta)) {
        $query->whereBetween('fecha', [$fechaDesde, $fechaHasta]);
    }

    $query->orderByRaw('IFNULL(hora_ingreso, fecha) DESC');
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
    Visita1::where('activo', true)->update(['activo' => false]);

    return redirect()->route('reporte.index')->with('success', 'El frontend ha sido vaciado correctamente.');
}
public function restaurarFrontend()
{
    Visita1::where('activo', false)->update(['activo' => true]);

    return redirect()->route('reporte.index')->with('success', 'El frontend ha sido restaurado correctamente.');
}

}
