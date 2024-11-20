<?php
// File: app/Http/Controllers/BotController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BotController extends Controller
{
    /**
     * Handle audio file upload and process it.
     */
    public function handleAudio(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:audio/mpeg,mp3,wav',
        ]);

        // Store the uploaded audio
        $audioPath = $request->file('audio')->store('audios', 'public');

        // Simulate processing the audio (replace this with actual logic)
        $responseText = "Audio recibido y procesado: " . $audioPath;

        return response()->json([
            'success' => true,
            'message' => $responseText,
        ]);
    }
}
