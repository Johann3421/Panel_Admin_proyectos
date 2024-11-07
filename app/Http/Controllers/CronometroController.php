<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trabajador;

class CronometroController extends Controller
{
    public function index()
    {
        // Consulta de trabajadores en receso
        $trabajadores = Trabajador::whereNotNull('hora_receso')
                                   ->whereNull('hora_vuelta')
                                   ->get();

        return view('cronometro', compact('trabajadores'));
    }

    // Método para finalizar el receso (opcional si necesitas esta funcionalidad)
    public function finalizarReceso($id)
    {
        $trabajador = Trabajador::findOrFail($id);
        $trabajador->hora_vuelta = now();
        $trabajador->save();

        return redirect()->route('cronometro.index')->with('success', 'Receso finalizado.');
    }
}
