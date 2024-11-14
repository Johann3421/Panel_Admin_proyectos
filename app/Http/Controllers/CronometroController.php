<?php

namespace App\Http\Controllers;

use App\Models\RecesoField;
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

        // Agregamos la carga de los campos RecesoField desde la base de datos
        $fields = RecesoField::all();

        return view('cronometro', compact('trabajadores', 'fields'));
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
            $horaReceso = Carbon::parse($trabajador->hora_receso, 'America/Lima');
            $ahora = Carbon::now('America/Lima');

            // Fin del receso basado en `duracion` en minutos
            $finReceso = $horaReceso->copy()->addMinutes($trabajador->duracion);

            // Calcula la duración restante en minutos, redondeando hacia arriba
            $duracionRestante = $finReceso->greaterThan($ahora)
                ? ceil($ahora->diffInMinutes($finReceso, false))
                : 0;

            // Calcula el exceso en minutos si ya pasó el tiempo límite del receso
            $exceso = $finReceso->lessThan($ahora)
                ? $ahora->diffInMinutes($finReceso)
                : 0;

            // Asignación final
            $trabajador->duracionRestante = $duracionRestante;
            $trabajador->exceso = $exceso;

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
