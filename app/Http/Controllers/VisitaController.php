<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita; // Asegúrate de que este modelo exista y esté configurado correctamente
use Illuminate\Support\Facades\Http;

class VisitaController extends Controller
{
    public function index(Request $request)
    {
        // Definir el límite de paginación
        $limite = 10;

        // Obtener parámetros de búsqueda y fecha desde la solicitud
        $busqueda = $request->input('busqueda');
        $fecha = $request->input('fecha');

        // Crear la consulta base de Visitas
        $query = Visita::query();

        // Filtro de visitas sin "hora_salida"
        $query->whereNull('hora_salida')->orWhere('hora_salida', '');

        // Filtro por fecha, si se proporciona
        if ($fecha) {
            $query->whereDate('fecha', $fecha);
        }

        // Filtro de búsqueda (nombre, DNI, motivo o lugar)
        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('dni', 'LIKE', "%{$busqueda}%")
                  ->orWhere('smotivo', 'LIKE', "%{$busqueda}%")
                  ->orWhere('lugar', 'LIKE', "%{$busqueda}%");
            });
        }

        // Paginación de resultados
        $visitas = $query->paginate($limite);

        // Retornar la vista 'visitas' con las variables necesarias
        return view('visitas', compact('visitas', 'busqueda', 'fecha'));
    }

    public function buscarDNI(Request $request)
    {
        $dni = $request->input('dni');
        $token = 'apis-token-10779.deFjdQHVSuenRlLS27jpqtmQ0SJV4hfj';
        $response = Http::withHeaders([
            'Authorization' => "Bearer $token"
        ])->get("https://api.apis.net.pe/v2/reniec/dni?numero={$dni}");

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'No se encontró el DNI'], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|max:8',
            'nombre' => 'required|string|max:255',
            'tipopersona' => 'required',
            'lugar' => 'required',
            'smotivo' => 'required',
        ]);

        Visita::create($request->all());
        return redirect()->route('visitas.index')->with('success', 'Visita registrada correctamente');
    }
}
