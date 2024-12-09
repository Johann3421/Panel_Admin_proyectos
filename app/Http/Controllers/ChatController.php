<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function handleChat(Request $request)
    {
        $userMessage = $request->input('message');

        if (!$userMessage) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        try {
            // Ejecuta el script Node.js con escapeshellarg para prevenir inyecciones
            $command = "node " . base_path('resources/js/groqHandler.js') . " " . escapeshellarg($userMessage);

            // Captura la salida del comando
            $output = shell_exec($command);

            if ($output) {
                // Intentamos decodificar la salida como JSON
                $decodedOutput = json_decode(trim($output), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return response()->json(['response' => $decodedOutput]);
                }

                // Si no es JSON, retornamos la salida tal cual
                return response()->json(['response' => $output]);
            } else {
                return response()->json(['error' => 'Failed to get response from Node.js script'], 500);
            }
        } catch (\Exception $e) {
            // Manejo de errores en caso de que el comando falle
            return response()->json(['error' => 'Error executing Node.js script: ' . $e->getMessage()], 500);
        }
    }
}