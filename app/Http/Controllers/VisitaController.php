<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Models\VisitaField; // Importa el modelo de campos personalizados
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class VisitaController extends Controller
{
    public function index(Request $request)
{
    // Configuración de los parámetros de búsqueda y filtros
    $limite = $request->input('limite', 10);
    $busqueda = $request->input('busqueda');
    $fecha = $request->input('fecha');

    // Consultas para las visitas
    $query = Visita::query();
    $query->whereNull('hora_salida')->orWhere('hora_salida', '');

    if ($fecha) {
        $query->whereDate('fecha', $fecha);
    }

    if ($busqueda) {
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'LIKE', "%{$busqueda}%")
                ->orWhere('dni', 'LIKE', "%{$busqueda}%")
                ->orWhere('smotivo', 'LIKE', "%{$busqueda}%")
                ->orWhere('lugar', 'LIKE', "%{$busqueda}%");
        });
    }

    // Paginación
    $visitas = $query->paginate($limite)->appends([
        'busqueda' => $busqueda,
        'fecha' => $fecha,
        'limite' => $limite
    ]);

    // Cargar los campos dinámicos desde la tabla visita_fields
    $fields = VisitaField::all();
    
    // Depuración: verifica el contenido de los campos
     // <- Añadir esta línea para ver los campos en el navegador

    // Retorna la vista 'visitas' con las variables necesarias
    return view('visitas', compact('visitas', 'busqueda', 'fecha', 'limite', 'fields'));
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
    // Recuperar los campos dinámicos
    $fields = VisitaField::all();

    // Construir reglas de validación dinámicamente
    $rules = [];
    foreach ($fields as $field) {
        $rule = [];
        if ($field->required) {
            $rule[] = 'required';
        }

        if ($field->type === 'text') {
            $rule[] = 'string';
            $rule[] = 'max:255';
        }

        if ($field->type === 'select' || $field->type === 'radio') {
            $options = json_decode($field->options, true);
            if (is_array($options)) {
                $rule[] = 'in:' . implode(',', $options);
            }
        }

        $rules[$field->name] = implode('|', $rule);
    }

    // Validar la solicitud
    $validated = $request->validate($rules);

    // Crear una nueva visita con los datos validados
    Visita::create(array_merge($validated, [
        'hora_ingreso' => Carbon::now('America/Lima'),
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
