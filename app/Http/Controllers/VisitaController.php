<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class VisitaController extends Controller
{
    // Muestra la lista de visitas, aplicando filtros y paginación
    public function index(Request $request)
    {
        // Limite predeterminado de 10, o toma el valor especificado en el request
        $limite = $request->input('limite', 10);
        $busqueda = $request->input('busqueda');
        $fecha = $request->input('fecha');

        // Crear la consulta base
        $query = Visita::query();

        // Filtrar visitas sin "hora_salida"
        $query->whereNull('hora_salida')->orWhere('hora_salida', '');

        // Filtrar por fecha, si se proporciona
        if ($fecha) {
            $query->whereDate('fecha', $fecha);
        }

        // Filtrar por búsqueda (nombre, DNI, motivo o lugar)
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('dni', 'LIKE', "%{$busqueda}%")
                    ->orWhere('smotivo', 'LIKE', "%{$busqueda}%")
                    ->orWhere('lugar', 'LIKE', "%{$busqueda}%");
            });
        }

        // Paginación de resultados con el límite especificado
        $visitas = $query->paginate($limite)->appends([
            'busqueda' => $busqueda,
            'fecha' => $fecha,
            'limite' => $limite
        ]);

        // Retornar la vista 'visitas' con las variables necesarias
        return view('visitas', compact('visitas', 'busqueda', 'fecha', 'limite'));
    }

    // Busca información de un DNI en una API externa
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

    // Registra una nueva visita y redirige a la lista de visitas
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|max:8',
            'nombre' => 'required|string|max:255',
            'tipopersona' => 'required',
            'lugar' => 'required',
            'smotivo' => 'required',
        ]);

        Visita::create(array_merge($request->all(), [
            'hora_ingreso' => Carbon::now('America/Lima'), // Asigna hora de ingreso en zona horaria de Perú
        ]));

        return redirect()->route('visitas.index')->with('success', 'Visita registrada correctamente');
    }

    public function registrarSalida($id)
    {
        $visita = Visita::findOrFail($id);
        $visita->hora_salida = Carbon::now('America/Lima'); // Asigna hora de salida en zona horaria de Perú
        $visita->save();

        return response()->json($visita); // Devuelve la visita completa actualizada
    }
}
