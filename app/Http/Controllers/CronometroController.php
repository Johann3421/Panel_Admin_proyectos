<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronometroController extends Controller
{
    public function index()
    {
        $trabajadores = DB::table('trabajadores')
            ->select('id', 'nombre', 'dni', 'hora_receso', 'hora_vuelta', 'duracion')
            ->whereNotNull('hora_receso')
            ->whereNull('hora_vuelta')
            ->get();

        return view('cronometro', compact('trabajadores'));
    }

    public function registrarReceso(Request $request)
    {
        $request->validate(['id' => 'required', 'duracion' => 'required|integer']);

        $horaReceso = now();
        DB::table('trabajadores')->where('id', $request->id)->update([
            'hora_receso' => $horaReceso,
            'duracion' => $request->duracion,
        ]);

        return response()->json(['status' => 'success', 'hora_receso' => $horaReceso->format('H:i:s')]);
    }

    public function finalizarReceso(Request $request)
    {
        $request->validate(['id' => 'required']);

        $horaVuelta = now();
        DB::table('trabajadores')->where('id', $request->id)->update(['hora_vuelta' => $horaVuelta]);

        return response()->json(['status' => 'success', 'hora_vuelta' => $horaVuelta->format('H:i:s')]);
    }

    public function tiemposRestantes()
    {
        $trabajadores = DB::table('trabajadores')
            ->whereNotNull('hora_receso')
            ->whereNull('hora_vuelta')
            ->get()
            ->map(function ($trabajador) {
                $trabajador->tiempo_restante = $this->calcularTiempoRestante($trabajador->hora_receso, $trabajador->duracion);
                return $trabajador;
            });

        return response()->json(['status' => 'success', 'trabajadores' => $trabajadores]);
    }

    public function buscarTrabajador(Request $request)
{
    $busqueda = $request->query('busqueda', '');

    if (!empty($busqueda)) {
        $trabajadores = DB::table('trabajadores')
            ->select('id', 'nombre', 'dni')
            ->where('nombre', 'LIKE', '%' . $busqueda . '%')
            ->orWhere('dni', 'LIKE', '%' . $busqueda . '%')
            ->limit(10)
            ->get();

        return response()->json($trabajadores);
    } else {
        return response()->json([]);
    }
}

    private function calcularTiempoRestante($horaReceso, $duracion)
    {
        $recesoInicio = strtotime($horaReceso);
        $ahora = time();
        $duracionEnSegundos = $duracion * 60;

        return max(0, $recesoInicio + $duracionEnSegundos - $ahora);
    }
}
