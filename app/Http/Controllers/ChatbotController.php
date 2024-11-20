<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Models\Receso;
use App\Models\Trabajador;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    /**
     * Manejar mensajes del chatbot.
     */
    public function handleMessage(Request $request)
    {
        $message = strtolower(trim($request->input('message')));

        // Corregir errores ortográficos en las palabras clave principales
        $normalizedMessage = $this->normalizeMessage($message);

        switch (true) {
            case Str::contains($normalizedMessage, ['hola', 'buenos días', 'buenas tardes', 'saludos']):
                return response()->json([
                    'response' => "👋 ¡Hola! Soy tu asistente para el sistema de Registro de Visitas. Puedes pedirme:\n- 'Registrar visita'\n- 'Listar visitas activas'\n- 'Registrar receso'\n- 'Listar recesos activos'."
                ]);

            case Str::contains($normalizedMessage, ['registrar receso']):
                return response()->json([
                    'response' => "Vamos a registrar un receso. Por favor responde las siguientes preguntas paso a paso:\n1️⃣ ¿Cuál es el ID del trabajador?"
                ]);

            // Captura del ID del trabajador
            case session()->missing('registro_receso.worker_id') && preg_match('/^\d+$/', $message):
                session(['registro_receso.worker_id' => $message]);
                return response()->json(['response' => "ID de trabajador registrado. Ahora, ¿cuántos minutos durará el receso?"]);

            // Captura de la duración del receso
            case session()->has('registro_receso.worker_id') && preg_match('/^\d+$/', $message):
                $workerId = session('registro_receso.worker_id');
                $duracion = $message;

                // Limpiar la sesión antes de continuar para evitar loops
                session()->forget('registro_receso');

                // Registrar el receso
                return $this->registrarReceso($workerId, $duracion);

            case Str::contains($normalizedMessage, ['listar recesos']):
                return $this->listarRecesos();

            default:
                return response()->json([
                    'response' => "🤖 Lo siento, no entendí eso. Por favor, intenta con algo como:\n- 'Registrar visita'\n- 'Listar visitas activas'\n- 'Registrar receso'\n- 'Listar recesos activos'."
                ]);
        }
    }

    /**
     * Normaliza un mensaje para corregir errores comunes de escritura.
     */
    private function normalizeMessage(string $message): string
    {
        $replacements = [
            'resgistrar' => 'registrar',
            'reseso' => 'receso',
            'tiemops' => 'tiempos',
            'busccar' => 'buscar',
        ];

        foreach ($replacements as $wrong => $correct) {
            $message = str_replace($wrong, $correct, $message);
        }

        return $message;
    }

    /**
     * Registrar un receso.
     */
    private function registrarReceso($workerId, $duracion)
    {
        $horaReceso = Carbon::now('America/Lima');

        // Verificar si ya existe un receso activo para este trabajador
        $recesoActivo = DB::table('recesos')
            ->where('trabajador_id', $workerId)
            ->where('estado', 'activo')
            ->first();

        if ($recesoActivo) {
            return response()->json(['response' => "⚠️ Ya hay un receso activo para este trabajador."]);
        }

        // Verificar si el trabajador existe
        $trabajador = DB::table('trabajadores')
            ->select('nombre', 'dni')
            ->where('id', $workerId)
            ->first();

        if (!$trabajador) {
            return response()->json(['response' => "❌ Trabajador no encontrado con ID: {$workerId}."]);
        }

        // Actualizar el trabajador para reflejar el inicio del receso
        $updated = DB::table('trabajadores')
            ->where('id', $workerId)
            ->update([
                'hora_receso' => $horaReceso,
                'duracion' => $duracion,
                'hora_vuelta' => null,
            ]);

        if (!$updated) {
            return response()->json(['response' => "❌ No se pudo actualizar el estado del trabajador."]);
        }

        // Insertar el nuevo receso en la tabla `recesos`
        DB::table('recesos')->insert([
            'trabajador_id' => $workerId,
            'nombre' => $trabajador->nombre,
            'dni' => $trabajador->dni,
            'duracion' => $duracion,
            'hora_receso' => $horaReceso,
            'estado' => 'activo',
        ]);

        return response()->json([
            'response' => "✅ Receso registrado correctamente para *{$trabajador->nombre}* con duración de {$duracion} minutos."
        ]);
    }

    /**
     * Listar recesos activos.
     */
    private function listarRecesos()
    {
        $recesosActivos = DB::table('recesos')
            ->where('estado', 'activo')
            ->get();

        if ($recesosActivos->isEmpty()) {
            return response()->json(['response' => "⚠️ No hay recesos activos en este momento."]);
        }

        $respuesta = "📋 Recesos activos:\n";
        foreach ($recesosActivos as $receso) {
            $respuesta .= "- *{$receso->nombre}* (DNI: {$receso->dni}), comenzó a las {$receso->hora_receso}, duración: {$receso->duracion} minutos.\n";
        }

        return response()->json(['response' => $respuesta]);
    }
    public function handleAudio(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimetypes:audio/webm,audio/wav,audio/mpeg',
        ]);

        // Guardar el archivo de audio
        $path = $request->file('audio')->store('audios', 'public');
        $audioUrl = Storage::url($path);

        // Simular transcripción de audio a texto (puedes integrar un servicio como Google Speech-to-Text aquí)
        $transcribedMessage = $this->transcribeAudio($path);

        // Manejar el mensaje como texto
        $responseText = $this->handleTextMessage($transcribedMessage);

        // Generar respuesta en audio (puedes integrar un servicio como Google TTS aquí)
        $responseAudioUrl = $this->generateAudioResponse($responseText['response']);

        return response()->json([
            'response' => $responseText['response'],
            'response_audio_url' => $responseAudioUrl,
        ]);
    }

    /**
     * Simular transcripción de audio a texto.
     * Aquí puedes integrar un servicio de Speech-to-Text real.
     */
    private function transcribeAudio($audioPath)
    {
        // Esto es solo un simulacro. Reemplaza esta lógica con un servicio de transcripción real.
        return "Mensaje transcrito del audio en {$audioPath}";
    }

    /**
     * Generar respuesta de audio a partir de texto.
     * Aquí puedes integrar un servicio de Text-to-Speech real.
     */
    private function generateAudioResponse($text)
    {
        $audioFileName = 'response_' . Str::random(10) . '.mp3';
        $filePath = storage_path('app/public/audios/' . $audioFileName);

        // Simular la generación de audio
        file_put_contents($filePath, "Audio generado para: {$text}");

        return Storage::url('audios/' . $audioFileName);
    }

    /**
     * Manejar mensajes de texto del chatbot (usado internamente).
     */
    private function handleTextMessage($message)
    {
        // Reutiliza la lógica existente para manejar texto
        return $this->handleMessage(new Request(['message' => $message]))->getData(true);
    }
}
