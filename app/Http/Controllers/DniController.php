<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DniController extends Controller
{
    public function buscarDni(Request $request)
    {
        $token1 = 'apis-token-10779.deFjdQHVSuenRlLS27jpqtmQ0SJV4hfj';
        $token2 = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'; // Token acortado para seguridad

        $dni = $request->input('dni');

        if (empty($dni)) {
            return response()->json(['success' => false, 'error' => 'El DNI es obligatorio.']);
        }

        // Primera consulta a la API
        $response = Http::withHeaders([
            'Referer' => 'https://apis.net.pe/consulta-dni-api',
            'Authorization' => 'Bearer ' . $token1,
        ])->get('https://api.apis.net.pe/v2/reniec/dni', ['numero' => $dni]);

        if ($response->successful() && !isset($response->json()['error'])) {
            $persona = $response->json();
            $nombre = trim($persona['nombres'] . " " . $persona['apellidoPaterno'] . " " . $persona['apellidoMaterno']);
            return response()->json(['success' => true, 'nombre' => $nombre]);
        }

        // Segunda consulta si la primera falla
        $response = Http::get('https://dniruc.apisperu.com/api/v1/dni/' . $dni, [
            'token' => $token2,
        ]);

        if ($response->successful() && !isset($response->json()['error'])) {
            $persona = $response->json();
            $nombre = trim($persona['nombres'] . " " . $persona['apellidoPaterno'] . " " . $persona['apellidoMaterno']);
            return response()->json(['success' => true, 'nombre' => $nombre]);
        }

        return response()->json(['success' => false, 'error' => 'No se encontró el DNI o ocurrió un error.']);
    }
}
