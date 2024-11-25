<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Models\Receso;
use App\Models\Trabajador;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

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

        $intent = $this->getIntent($message);

        switch ($intent) {
            case 'registrar visita':
                session(['registro_visita' => []]);
                return response()->json([
                    'response' => "Iniciando registro de visita. ¿Cuál es el DNI del trabajador?"
                ]);
            case 'listar visitas':
                return $this->listarVisitas();
            case 'registrar receso':
                return response()->json([
                    'response' => "¿Cuál es el ID del trabajador?"
                ]);
            case 'listar recesos':
                return $this->listarRecesos();
            default:
                return response()->json([
                    'response' => "🤖 No entendí eso. Intenta con:\n- 'Registrar visita'\n- 'Listar visitas activas'\n- 'Registrar receso'\n- 'Listar recesos activos'."
                ]);
        }

        if (session()->has('registro_visita')) {
            return $this->handleRegistroVisita($normalizedMessage);
        }

        if (session()->has('listar_visitas')) {
            return $this->listarVisitas($normalizedMessage);
        }

        switch (true) {
            case Str::contains($normalizedMessage, ['hola', 'buenos días', 'buenas tardes', 'saludos']):
                return response()->json([
                    'response' => "👋 ¡Hola! Soy tu asistente para el sistema de Registro de Visitas. Puedes pedirme:\n- 'Registrar visita'\n- 'Listar visitas activas'\n- 'Registrar receso'\n- 'Listar recesos activos'."
                ]);
            case Str::contains($normalizedMessage, ['registrar visita']):
                session(['registro_visita' => []]);
                return response()->json([
                    'response' => "Vamos a registrar una visita. Por favor responde las siguientes preguntas paso a paso:\n1️⃣ ¿Cuál es el DNI del trabajador?"
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

    private function getIntent(string $message): string
    {
        $client = new Client();
        try {
            $response = $client->post('https://api-inference.huggingface.co/models/facebook/bart-large-mnli', [
                'headers' => [
                    'Authorization' => 'Bearer TU_API_KEY'
                ],
                'json' => [
                    'inputs' => $message,
                    'parameters' => ['candidate_labels' => ['registrar visita', 'listar visitas', 'registrar receso', 'listar recesos']],
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['labels'][0] ?? 'unknown'; // Retornar la intención más probable o 'unknown'
        } catch (\Exception $e) {
            // Manejar errores de la API
            \Log::error("Error en Hugging Face API: " . $e->getMessage());
            return 'unknown';
        }
    }

    private function handleRegistroVisita(string $message)
    {
        $data = session('registro_visita');

        if (!isset($data['dni']) && preg_match('/^\d{8}$/', $message)) {
            $data['dni'] = $message;
            session(['registro_visita' => $data]);
            return response()->json(['response' => "👍 Gracias. Ahora, ¿cuál es el nombre del trabajador?"]);
        }

        if (!isset($data['nombre']) && strlen($message) > 2) {
            $data['nombre'] = $message;
            session(['registro_visita' => $data]);
            return response()->json(['response' => "👍 Gracias. Ahora, ¿cuál es el tipo de persona (por ejemplo, 'empleado' o 'externo')?"]);
        }

        if (!isset($data['tipo_persona']) && strlen($message) > 2) {
            $data['tipo_persona'] = $message;
            session(['registro_visita' => $data]);
            return response()->json(['response' => "👍 Gracias. Ahora, ¿cuál es el lugar de trabajo?"]);
        }

        if (!isset($data['lugar']) && strlen($message) > 2) {
            $data['lugar'] = $message;
            session(['registro_visita' => $data]);
            return response()->json(['response' => "👍 Gracias. Por último, ¿cuál es el motivo de la visita?"]);
        }

        if (!isset($data['motivo']) && strlen($message) > 2) {
            $data['motivo'] = $message;

            // Aquí puedes guardar en la base de datos si es necesario
            Visita::create($data);

            session()->forget('registro_visita');
            return response()->json(['response' => "✅ Visita registrada correctamente."]);
        }

        return response()->json(['response' => "⚠️ Por favor, responde a la pregunta actual."]);
    }

    private function listarVisitas()
    {
        // Obtener todas las visitas activas (que no tienen hora de salida)
        $visitasActivas = DB::table('visitas')
            ->whereNull('hora_salida')
            ->get();

        if ($visitasActivas->isEmpty()) {
            return response()->json([
                'response' => "⚠️ No hay visitas activas en este momento."
            ]);
        }

        $respuesta = "📋 Visitas activas:\n";
        foreach ($visitasActivas as $visita) {
            $respuesta .= "- *{$visita->nombre}* (DNI: {$visita->dni}), motivo: {$visita->motivo}, entrada: {$visita->hora_entrada}.\n";
        }

        return response()->json([
            'response' => $respuesta
        ]);
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
