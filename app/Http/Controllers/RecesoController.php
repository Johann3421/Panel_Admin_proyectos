<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trabajador;
use App\Models\Receso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecesoController extends Controller
{
    public function registrarReceso(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'duracion' => 'required|integer'
        ]);

        $worker_id = $request->input('id');
        $duracion = $request->input('duracion');
        $hora_receso = Carbon::now('America/Lima')->toDateTimeString();

        // Iniciar una transacción para asegurar la consistencia de datos
        DB::beginTransaction();

        try {
            // Actualizar la información del trabajador en la tabla `trabajadores`
            $trabajador = Trabajador::where('id', $worker_id)->first();
            if (!$trabajador) {
                return response()->json(['status' => 'error', 'message' => 'Trabajador no encontrado.'], 404);
            }

            $trabajador->update([
                'hora_receso' => $hora_receso,
                'duracion' => $duracion,
                'hora_vuelta' => null
            ]);

            // Registrar el receso en la tabla `recesos`
            $receso = Receso::create([
                'trabajador_id' => $worker_id,
                'nombre' => $trabajador->nombre,
                'dni' => $trabajador->dni,
                'duracion' => $duracion,
                'hora_receso' => $hora_receso,
                'estado' => 'activo'
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'id' => $worker_id,
                'nombre' => $trabajador->nombre,
                'dni' => $trabajador->dni,
                'hora_receso' => $hora_receso,
                'duracion' => $duracion
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error al registrar el receso.'], 500);
        }
    }
}
