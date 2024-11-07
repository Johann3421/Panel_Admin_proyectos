<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receso;

class RecesoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('busqueda', '');
        $fechaDesde = $request->input('desde', '');
        $fechaHasta = $request->input('hasta', '');
        $limite = 10;

        $recesosQuery = Receso::where(function ($query) use ($busqueda) {
            $query->where('nombre', 'like', '%' . $busqueda . '%')
                  ->orWhere('dni', 'like', '%' . $busqueda . '%');
        });

        if ($fechaDesde && $fechaHasta) {
            $recesosQuery->whereBetween('hora_receso', [$fechaDesde, $fechaHasta]);
        }

        $recesos = $recesosQuery->paginate($limite);
        
        // Cargar la vista renombrada 'recesos.blade.php'
        return view('recesos', compact('recesos', 'busqueda', 'fechaDesde', 'fechaHasta'));
    }
}
