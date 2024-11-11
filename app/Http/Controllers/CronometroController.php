<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $request->validate([
            'id' => 'required|integer',
            'duracion' => 'required|integer'
        ]);

        $workerId = $request->input('id');
        $duracionMinutos = $request->input('duracion');
        $horaReceso = Carbon::now('America/Lima');

        // Verifica si ya existe un receso activo
        $recesoActivo = DB::table('recesos')
            ->where('trabajador_id', $workerId)
            ->where('estado', 'activo')
            ->first();

        if ($recesoActivo) {
            return response()->json(['status' => 'error', 'message' => 'Ya hay un receso activo para este trabajador.']);
        }

        // Actualizar el trabajador para reflejar el inicio del receso
        $updated = DB::table('trabajadores')
            ->where('id', $workerId)
            ->update([
                'hora_receso' => $horaReceso,
                'duracion' => $duracionMinutos, // Almacena la duración en minutos, no en segundos
                'hora_vuelta' => null
            ]);

        if ($updated) {
            $trabajador = DB::table('trabajadores')
                ->select('nombre', 'dni')
                ->where('id', $workerId)
                ->first();

            if ($trabajador) {
                // Inserta el nuevo receso en la tabla `recesos`
                DB::table('recesos')->insert([
                    'trabajador_id' => $workerId,
                    'nombre' => $trabajador->nombre,
                    'dni' => $trabajador->dni,
                    'duracion' => $duracionMinutos, // Duración en minutos
                    'hora_receso' => $horaReceso,
                    'estado' => 'activo'
                ]);

                return response()->json([
                    'status' => 'success',
                    'id' => $workerId,
                    'nombre' => $trabajador->nombre,
                    'dni' => $trabajador->dni,
                    'hora_receso' => $horaReceso->format('H:i:s'),
                    'duracion' => $duracionMinutos // Envía la duración en minutos para el frontend
                ]);
            }
            return response()->json(['status' => 'error', 'message' => 'Trabajador no encontrado.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Error al registrar el receso.']);
    }

    public function finalizarReceso(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $workerId = $request->input('id');
        $horaVuelta = Carbon::now('America/Lima');

        $receso = DB::table('recesos')
            ->where('trabajador_id', $workerId)
            ->where('estado', 'activo')
            ->first();

        if ($receso) {
            $horaReceso = Carbon::parse($receso->hora_receso);
            
            // Calcula el tiempo de receso usado en minutos
            $duracionUsada = $horaReceso->diffInMinutes($horaVuelta);
            $duracionProgramada = $receso->duracion; // Duración en minutos como está en la BD
            $exceso = max(0, $duracionUsada - $duracionProgramada);

            // Actualiza el receso con los valores finales, usando minutos
            DB::table('recesos')
                ->where('trabajador_id', $workerId)
                ->where('estado', 'activo')
                ->update([
                    'hora_vuelta' => $horaVuelta,
                    'duracion' => $duracionProgramada, // Mantener el valor inicial en minutos
                    'exceso' => $exceso, // Exceso en minutos
                    'estado' => 'finalizado'
                ]);

            // Actualiza el trabajador para indicar que terminó el receso
            DB::table('trabajadores')->where('id', $workerId)->update(['hora_vuelta' => $horaVuelta]);

            return response()->json(['status' => 'success', 'hora_vuelta' => $horaVuelta->format('H:i:s')]);
        }

        return response()->json(['status' => 'error', 'message' => 'No hay receso activo para este trabajador.']);
    }

    public function tiemposRestantes()
    {
        $trabajadores = DB::table('trabajadores')
            ->whereNotNull('hora_receso')
            ->whereNull('hora_vuelta')
            ->get()
            ->map(function ($trabajador) {
                $horaReceso = Carbon::parse($trabajador->hora_receso);
                $finReceso = $horaReceso->copy()->addMinutes($trabajador->duracion);
                $ahora = Carbon::now('America/Lima');

                $intervalo = $finReceso->diffInSeconds($ahora, false);
                $trabajador->tiempo_restante = abs($intervalo);
                $trabajador->en_tiempo_extra = $intervalo < 0;

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
}
