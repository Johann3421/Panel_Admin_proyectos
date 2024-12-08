<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
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
    try {
        $message = strtolower(trim($request->input('message')));

        // Normalizar mensaje
        $normalizedMessage = $this->normalizeMessage($message);
        
    $message = strtolower(trim($request->input('message')));

    // Normalizar mensaje para evitar errores ortográficos
    $normalizedMessage = $this->normalizeMessage($message);

    // Determinar intención solo si no hay un flujo activo
    $intent = null;
    if (!session()->has('registro_visita') && !session()->has('registro_receso')) {
        $intent = $this->getIntent($normalizedMessage);
    }

    // Comando explícito para cancelar cualquier flujo activo
    if ($normalizedMessage === 'cancelar') {
        if (session()->has('registro_visita')) {
            session()->forget('registro_visita');
            return response()->json(['response' => "❌ El proceso de registro de visita ha sido cancelado."]);
        }
        if (session()->has('registro_receso')) {
            session()->forget('registro_receso');
            return response()->json(['response' => "❌ El proceso de registro de receso ha sido cancelado."]);
        }
        return response()->json(['response' => "⚠️ No hay ningún proceso activo para cancelar."]);
    }

    // Lógica principal del chatbot
    switch (true) {
        // Saludos generales
        case Str::contains($normalizedMessage, ['hola', 'buenos días', 'buenas tardes', 'saludos']):
            return response()->json([
                'response' => "👋 ¡Hola! Soy tu asistente para el sistema de Registro de Visitas. Puedes pedirme:\n- 'Registrar visita'\n- 'Listar visitas'\n- 'Registrar receso'\n- 'Listar recesos activos'\n Si te equivocas, puedes corregir un campo con la palabra 'corregir'\n y el campo que deseas corregir."
            ]);

        // Iniciar flujo de registro de visita
        case $intent === 'registrar visita':
            if (session()->has('registro_receso')) {
                return response()->json([
                    'response' => "⚠️ Actualmente estás registrando un receso. Finaliza o cancela ese proceso para registrar una visita."
                ]);
            }
            session(['registro_visita' => []]); // Inicializa el flujo de registro
            return response()->json([
                'response' => "Vamos a registrar una visita. Por favor responde las siguientes preguntas paso a paso:\n1️⃣ ¿Cuál es el DNI del trabajador?"
            ]);

        // Manejar flujo activo de registro de visita
        case session()->has('registro_visita'):
            return $this->handleRegistroVisita($normalizedMessage);

        // Iniciar flujo de registro de receso
        case $intent === 'registrar receso':
            if (session()->has('registro_visita')) {
                return response()->json([
                    'response' => "⚠️ Actualmente estás registrando una visita. Finaliza o cancela ese proceso para registrar un receso."
                ]);
            }
            session(['registro_receso' => []]);
            return response()->json([
                'response' => "Vamos a registrar un receso. Por favor responde las siguientes preguntas paso a paso:\n1️⃣ ¿Cuál es el ID del trabajador?"
            ]);

        // Manejar flujo activo de registro de receso
        case session()->has('registro_receso') && session()->missing('registro_receso.worker_id') && preg_match('/^\d+$/', $message):
            session(['registro_receso.worker_id' => $message]);
            return response()->json(['response' => "ID de trabajador registrado. Ahora, ¿cuántos minutos durará el receso?"]);

        case session()->has('registro_receso.worker_id') && preg_match('/^\d+$/', $message):
            $workerId = session('registro_receso.worker_id');
            $duracion = $message;

            session()->forget('registro_receso'); // Limpiar sesión para evitar conflictos
            return $this->registrarReceso($workerId, $duracion);

        // Listar visitas activas
        case $intent === 'listar visitas':
            return $this->listarVisitas();

        // Listar recesos activos
        case $intent === 'listar recesos':
            return $this->listarRecesos();

        // Respuesta predeterminada
        default:
            return response()->json([
                'response' => "🤖 Lo siento, no entendí eso. Por favor, intenta con algo como:\n- 'Registrar visita'\n- 'Listar visitas activas'\n- 'Registrar receso'\n- 'Listar recesos activos'."
            ]);
    }
    } catch (\Exception $e) {
        // Registrar el error para depuración
        \Log::error("Error en el chatbot: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

        // Retornar error como JSON
        return response()->json([
            'response' => "❌ Ocurrió un error inesperado. Por favor, intenta de nuevo más tarde.",
            'error' => $e->getMessage() // Solo habilitar en desarrollo
        ], 500);
    }
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
    // Constantes para los campos requeridos y sus mensajes
    $fieldMessages = [
        'dni' => 'Por favor, ingresa el nombre del trabajador.',
        'nombre' => 'Por favor, ingresa el tipo de persona (por ejemplo, "Natural" o "Pública/Privada").',
        'tipopersona' => 'Por favor, ingresa el lugar de trabajo.',
        'lugar' => 'Por favor, ingresa el motivo de la visita.',
        'smotivo' => '✅ Visita registrada correctamente.'
    ];

    // Cancelar el proceso
    if (strtolower($message) === 'cancelar') {
        session()->forget('registro_visita');
        return response()->json(['response' => "❌ El proceso de registro ha sido cancelado. Si deseas iniciar de nuevo, escribe 'registrar visita'."]);
    }

    // Reiniciar la sesión si el usuario empieza un nuevo registro
    if (strtolower($message) === 'registrar visita') {
        session()->forget('registro_visita');
        return response()->json(['response' => "Vamos a registrar una visita. Por favor responde las siguientes preguntas paso a paso: 1️⃣ ¿Cuál es el DNI del trabajador?"]);
    }

    // Obtener o inicializar la sesión de registro de visita
    $data = session('registro_visita', []);

    // Manejo de corrección de campos
    if (Str::startsWith($message, 'corregir')) {
        $field = strtolower(trim(Str::replace('corregir', '', $message))); // Extraer el campo a corregir
        if (array_key_exists($field, $fieldMessages)) {
            unset($data[$field]); // Eliminar el campo para forzar la corrección
            session(['registro_visita' => $data]);
            return response()->json(['response' => "🔄 Has solicitado corregir el campo '{$field}'. {$fieldMessages[$field]}"]);
        }
        return response()->json(['response' => "⚠️ No se puede corregir el campo '{$field}'. Los campos válidos son: " . implode(', ', array_keys($fieldMessages)) . "."]);
    }

    // Procesar el campo pendiente más importante (en orden)
    foreach ($fieldMessages as $field => $prompt) {
        if (!isset($data[$field])) {
            $validationResult = $this->validateField($field, $message);
            if ($validationResult !== true) {
                return response()->json(['response' => $validationResult]); // Respuesta de error en validación
            }

            // Guardar el valor validado
            $data[$field] = $message;
            session(['registro_visita' => $data]);

            // Si no es el último campo, solicitar el siguiente
            if ($field !== 'smotivo') {
                return response()->json(['response' => "👍 Gracias. {$prompt}"]);
            }

            // Último campo: Registrar, incluir hora de ingreso/salida
            $data['hora_ingreso'] = now('America/Lima')->toDateTimeString();
            Visita::create($data);
            session()->forget('registro_visita'); // Limpiar la sesión tras completar
            return response()->json(['response' => "✅ Visita registrada correctamente con hora de ingreso: {$data['hora_ingreso']}"]);
        }
    }

    // Si todos los campos están completos, evitar repeticiones
    return response()->json(['response' => "✅ Ya se ha completado el registro de esta visita."]);
}

/**
 * Valida el valor de un campo según sus reglas específicas.
 *
 * @param string $field Nombre del campo a validar
 * @param string $value Valor del mensaje recibido
 * @return string|bool Mensaje de error si no es válido, true si es válido
 */
private function validateField(string $field, string $value)
{
    switch ($field) {
        case 'dni':
            if (!preg_match('/^\d{8}$/', $value)) {
                return "⚠️ Por favor, proporciona un DNI válido (8 dígitos).";
            }
            break;

        case 'nombre':
        case 'tipopersona':
        case 'lugar':
        case 'smotivo':
            if (strlen($value) <= 2) {
                return "⚠️ Por favor, proporciona un valor válido para el campo '{$field}'.";
            }
            break;

        default:
            return "⚠️ Campo desconocido.";
    }
    return true;
}


    private function listarVisitas()
{
    try {
        // Obtener todas las visitas activas (que no tienen hora de salida)
        $visitasActivas = DB::table('visitas')
            ->whereNull('hora_salida')
            ->get();

        if ($visitasActivas->isEmpty()) {
            return response()->json([
                'response' => "⚠️ No hay visitas activas en este momento."
            ], 200);
        }

        // Construir la respuesta
        $respuesta = "📋 Visitas activas:\n";
        foreach ($visitasActivas as $visita) {
            // Manejo seguro de los datos para evitar errores
            $nombre = $visita->nombre ?? 'Sin nombre';
            $dni = $visita->dni ?? 'Sin DNI';
            $motivo = $visita->smotivo ?? 'Sin motivo';
            $horaEntrada = $visita->hora_ingreso ?? 'Sin hora de entrada';

            $respuesta .= "- *{$nombre}* (DNI: {$dni}), motivo: {$motivo}, entrada: {$horaEntrada}.\n";
        }

        return response()->json([
            'response' => $respuesta
        ], 200);

    } catch (\Exception $e) {
        // Enviar detalles básicos del error en modo producción
        return response()->json([
            'response' => "❌ Ocurrió un error al intentar listar las visitas. Por favor, contacta con soporte.",
            'debug' => env('APP_DEBUG') ? $e->getMessage() : "Error interno en el servidor"
        ], 500);
    }
}

    /**
     * Normaliza un mensaje para corregir errores comunes de escritura.
     */
    private function normalizeMessage(string $message): string
{
    $replacements = [
        // Correcciones comunes de escritura
        'resgistrar' => 'registrar',
        'reseso' => 'receso',
        'tiemops' => 'tiempos',
        'busccar' => 'buscar',

        // Palabras clave relacionadas con intenciones
        'visitaa' => 'visita',
        'registrarr' => 'registrar',
        'listaar' => 'listar',
        'visitas activass' => 'visitas activas',
        'recesoss' => 'recesos',
        'mostarr' => 'mostrar',
        'crearrr' => 'crear',
    ];

    // Realiza las correcciones en el mensaje
    foreach ($replacements as $wrong => $correct) {
        $message = str_replace($wrong, $correct, $message);
    }

    // Normaliza espacios múltiples
    $message = preg_replace('/\s+/', ' ', $message);

    // Retorna el mensaje corregido
    return trim($message);
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

}
