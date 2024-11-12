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
    $duracionMinutos = $request->input('duracion'); // Duración en minutos
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
            'duracion' => $duracionMinutos, // Almacena la duración en minutos
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
        $horaReceso = Carbon::parse($receso->hora_receso, 'America/Lima');
        $duracionProgramada = (int) $receso->duracion;
        $horaLimiteReceso = $horaReceso->copy()->addMinutes($duracionProgramada);

        // Si estamos dentro del tiempo del receso programado, calcular tiempo restante
        if ($horaVuelta->lessThanOrEqualTo($horaLimiteReceso)) {
            $duracionRestante = max(0, $horaVuelta->diffInMinutes($horaLimiteReceso, false));
            $exceso = 0;  // No hay exceso si estamos dentro del tiempo límite
        } else {
            // Si ya pasó el tiempo del receso, calculamos el exceso y dejamos tiempo restante en 0
            $duracionRestante = 0;
            $exceso = (int) round($horaVuelta->diffInMinutes($horaLimiteReceso));
        }

        // Log de valores calculados
        \Log::info('Duración Programada:', ['duracion' => $duracionProgramada]);
        \Log::info('Duración Restante Calculada:', ['duracionRestante' => $duracionRestante]);
        \Log::info('Exceso Calculado:', ['exceso' => $exceso]);
        

        // Actualizar receso en la base de datos
        DB::table('recesos')
            ->where('trabajador_id', $workerId)
            ->where('estado', 'activo')
            ->update([
                'hora_vuelta' => $horaVuelta,
                'duracion' => $duracionProgramada,
                'exceso' => $exceso,
                'estado' => 'finalizado'
            ]);

        DB::table('trabajadores')->where('id', $workerId)->update(['hora_vuelta' => $horaVuelta]);

        // Responder al frontend con duracionRestante correcta
        return response()->json([
            'status' => 'success',
            'hora_vuelta' => $horaVuelta->format('H:i:s'),
            'duracionRestante' => $duracionRestante
        ]);
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

            // Calcula la duración restante en minutos
            $intervaloMinutos = ceil($finReceso->diffInMinutes($ahora, false));
            $duracionRestante = max(0, $intervaloMinutos);

            // Log detallado
            \Log::info('Cálculo de Tiempos Restantes:', [
                'trabajador_id' => $trabajador->id,
                'hora_receso' => $horaReceso->format('H:i:s'),
                'fin_receso' => $finReceso->format('H:i:s'),
                'hora_actual' => $ahora->format('H:i:s'),
                'intervalo_minutos' => $intervaloMinutos,
                'duracionRestante' => $duracionRestante,
                'en_tiempo_extra' => $intervaloMinutos < 0
            ]);

            // Asignación de valores finales
            $trabajador->duracionRestante = $duracionRestante;
            $trabajador->en_tiempo_extra = $intervaloMinutos < 0;

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
