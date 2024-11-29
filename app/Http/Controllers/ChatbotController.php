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

    // Normalizar mensaje para evitar errores ortográficos
    $normalizedMessage = $this->normalizeMessage($message);

    // Determinar intención usando el clasificador, pero no sobrescribir si está en un flujo activo
    if (!session()->has('registro_visita') && !session()->has('registro_receso')) {
        $intent = $this->classifyIntent($normalizedMessage);
    } else {
        $intent = null;
    }

    switch (true) {
        case Str::contains($normalizedMessage, ['hola', 'buenos días', 'buenas tardes', 'saludos']):
            return response()->json([
                'response' => "👋 ¡Hola! Soy tu asistente para el sistema de Registro de Visitas. Puedes pedirme:\n- 'Registrar visita'\n- 'Listar visitas activas'\n- 'Registrar receso'\n- 'Listar recesos activos'."
            ]);

        case $intent === 'registrar_visita':
            session(['registro_visita' => []]); // Inicializa el flujo de registro
            return response()->json([
                'response' => "Vamos a registrar una visita. Por favor responde las siguientes preguntas paso a paso:\n1️⃣ ¿Cuál es el DNI del trabajador?"
            ]);

        case session()->has('registro_visita'): // Manejo del flujo activo de registro de visita
            return $this->handleRegistroVisita($normalizedMessage);

        case $intent === 'registrar_receso':
            session(['registro_receso' => []]);
            return response()->json([
                'response' => "Vamos a registrar un receso. Por favor responde las siguientes preguntas paso a paso:\n1️⃣ ¿Cuál es el ID del trabajador?"
            ]);

        case session()->has('registro_receso') && session()->missing('registro_receso.worker_id') && preg_match('/^\d+$/', $message):
            session(['registro_receso.worker_id' => $message]);
            return response()->json(['response' => "ID de trabajador registrado. Ahora, ¿cuántos minutos durará el receso?"]);

        case session()->has('registro_receso.worker_id') && preg_match('/^\d+$/', $message):
            $workerId = session('registro_receso.worker_id');
            $duracion = (int)$message;

            // Registrar receso y limpiar sesión
            $response = $this->registrarReceso($workerId, $duracion);
            session()->forget('registro_receso');

            return $response;

        case $intent === 'listar_visitas':
            return $this->listarVisitas();

        case $intent === 'listar_recesos':
            return $this->listarRecesos();

        default:
            return response()->json([
                'response' => "🤖 Lo siento, no entendí eso. Por favor, intenta con algo como:\n- 'Registrar visita'\n- 'Listar visitas activas'\n- 'Registrar receso'\n- 'Listar recesos activos'."
            ]);
    }
}


public function classifyIntent($message)
    {
        $model = config('chatbot_model'); // Cargar el modelo desde config

        $tokens = array_filter(preg_split('/\W+/', strtolower($message))); // Tokenizar
        $vocab = $model['vocab'];
        $wordCounts = $model['wordCounts'];
        $intentCounts = $model['intentCounts'];

        $totalIntents = array_sum($intentCounts);
        $bestIntent = null;
        $bestScore = -INF;

        foreach ($intentCounts as $intent => $count) {
            $score = log($count / $totalIntents); // Prior

            foreach ($tokens as $token) {
                $wordFrequency = $wordCounts[$intent][$token] ?? 0.01; // Suavizado
                $score += log($wordFrequency / count($vocab));
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIntent = $intent;
            }
        }

        return $bestIntent;
    }


    private function getIntent(string $message): string
{
    // Palabras clave asociadas con cada intención
    $intents = [
        'registrar visita' => ['registrar visita', 'nueva visita', 'crear visita'],
        'listar visitas' => ['listar visitas', 'visitas activas', 'mostrar visitas'],
        'registrar receso' => ['registrar receso', 'nuevo receso', 'crear receso'],
        'listar recesos' => ['listar recesos', 'recesos activos', 'mostrar recesos'],
    ];

    // Buscar coincidencias en las palabras clave
    foreach ($intents as $intent => $keywords) {
        foreach ($keywords as $keyword) {
            if (Str::contains($message, $keyword)) {
                return $intent; // Retorna la intención coincidente
            }
        }
    }

    return 'unknown'; // Si no coincide ninguna intención
}

private function handleRegistroVisita(string $message)
{
    // Obtener o inicializar la sesión de registro de visita
    $data = session('registro_visita', []);

    // Paso 1: Captura del DNI
    if (!isset($data['dni'])) {
        if (preg_match('/^\d{8}$/', $message)) { // Validar formato de DNI
            $data['dni'] = $message;
            session(['registro_visita' => $data]);
            return response()->json(['response' => "👍 Gracias. Ahora, ¿cuál es el nombre del trabajador?"]);
        }
        return response()->json(['response' => "⚠️ Por favor, proporciona un DNI válido (8 dígitos)."]);
    }

    // Paso 2: Captura del nombre
    if (!isset($data['nombre'])) {
        if (strlen($message) > 2) { // Asegurar que el nombre sea significativo
            $data['nombre'] = $message;
            session(['registro_visita' => $data]);
            return response()->json(['response' => "👍 Gracias. Ahora, ¿cuál es el tipo de persona (por ejemplo, 'Natural' o 'Publica/Privada')?"]);
        }
        return response()->json(['response' => "⚠️ Por favor, proporciona un nombre válido."]);
    }

    // Paso 3: Captura del tipo de persona
    if (!isset($data['tipopersona'])) {
        if (strlen($message) > 2) { // Validar un tipo significativo
            $data['tipopersona'] = $message;
            session(['registro_visita' => $data]);
            return response()->json(['response' => "👍 Gracias. Ahora, ¿cuál es el lugar de trabajo?"]);
        }
        return response()->json(['response' => "⚠️ Por favor, proporciona un tipo de persona válido (por ejemplo, 'Natural' o 'Publica/Privada')."]);
    }

    // Paso 4: Captura del lugar de trabajo
    if (!isset($data['lugar'])) {
        if (strlen($message) > 2) { // Validar un lugar significativo
            $data['lugar'] = $message;
            session(['registro_visita' => $data]);
            return response()->json(['response' => "👍 Gracias. Por último, ¿cuál es el motivo de la visita?"]);
        }
        return response()->json(['response' => "⚠️ Por favor, proporciona un lugar de trabajo válido."]);
    }

    // Paso 5: Captura del motivo de la visita
    if (!isset($data['smotivo'])) {
        if (strlen($message) > 2) { // Validar un motivo significativo
            $data['smotivo'] = $message;

            // Guardar en la base de datos
            Visita::create($data);

            session()->forget('registro_visita'); // Limpiar la sesión tras completar
            return response()->json(['response' => "✅ Visita registrada correctamente."]);
        }
        return response()->json(['response' => "⚠️ Por favor, proporciona un motivo válido."]);
    }

    // Mensaje por defecto si algo sale mal
    return response()->json(['response' => "⚠️ Ocurrió un error. Por favor, inténtalo de nuevo."]);
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
