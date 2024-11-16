<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Http\Request;
use App\Models\Visita;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $config = [];
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
        $botman = BotManFactory::create($config);

        // Mensaje de bienvenida con botones
        $botman->hears('hola|Hola', function (BotMan $bot) {
            $question = Question::create("👋 ¡Hola! Soy tu asistente para el sistema de Registro de Visitas. ¿En qué puedo ayudarte?")
                ->addButtons([
                    Button::create('Registrar visita')->value('registrar_visita'),
                    Button::create('Listar visitas activas')->value('listar_visitas'),
                    Button::create('Registrar Receso')->value('registrar_receso'),
                    Button::create('Listar recesos activos')->value('listar_recesos'),
                ]);

            $bot->reply($question);
        });

        // Registrar visita
        $botman->hears('registrar_visita', function (BotMan $bot) {
            $bot->reply("Por favor, proporciona los datos de la visita separados por comas:\n`DNI, Nombre, Tipo de Persona, Lugar, Motivo`");
        });

        // Patrón genérico para registrar visita
        $botman->hears('{dni},{nombre},{tipopersona},{lugar},{smotivo}', function (BotMan $bot, $dni, $nombre, $tipopersona, $lugar, $smotivo) {
            if (empty($dni) || empty($nombre) || empty($tipopersona) || empty($lugar) || empty($smotivo)) {
                $bot->reply("❌ Datos incompletos. Asegúrate de enviar los datos en el formato correcto: `DNI, Nombre, Tipo de Persona, Lugar, Motivo`.");
                return;
            }

            // Crea el registro en la base de datos
            $visita = Visita::create([
                'dni' => $dni,
                'nombre' => $nombre,
                'tipopersona' => $tipopersona,
                'lugar' => $lugar,
                'smotivo' => $smotivo,
                'hora_ingreso' => Carbon::now('America/Lima'),
                'fecha' => Carbon::now('America/Lima')->format('Y-m-d'),
            ]);

            $bot->reply("✅ Visita registrada correctamente para *{$nombre}*.");
        });

        // Listar visitas activas
        $botman->hears('listar_visitas', function (BotMan $bot) {
            $visitas = Visita::whereNull('hora_salida')->get();

            if ($visitas->isEmpty()) {
                $bot->reply("⚠️ No hay visitas activas en este momento.");
            } else {
                $respuesta = "📋 Visitas activas:\n";
                foreach ($visitas as $visita) {
                    $respuesta .= "- *{$visita->nombre}* (DNI: {$visita->dni}) ingresó a las {$visita->hora_ingreso}\n";
                }
                $bot->reply($respuesta);
            }
        });

        $botman->hears('registrar_receso', function (BotMan $bot) {
            $bot->reply("Por favor, proporciona los datos de la visita separados por comas:\n`id, nombre, dni, hora_receso, duracion`");
        });

        // Patrón genérico para registrar visita
        $botman->hears('{id},{nombre},{dni},{hora_receso},{duracion}', function (BotMan $bot, $id, $nombre, $dni, $hora_receso, $duracion) {
            if (empty($id) || empty($nombre) || empty($dni) || empty($hora_receso) || empty($duracion)) {    
                $bot->reply("❌ Datos incompletos. Asegúrate de enviar los datos en el formato correcto: `id, nombre, dni, hora_receso, duracion`.");
                return;
            }

            // Crea el registro en la base de datos
            $trabajador = Trabajador::create([
                'nombre' => $nombre,
                'dni' => $dni,
                'hora_receso' => $hora_receso,
                'duracion' => $duracion,
            ]);

            $bot->reply("✅ Receso registrado correctamente para *{$nombre}*.");
        });
        // Listar recesos activos
        $botman->hears('listar_recesos', function (BotMan $bot) {
            $trabajadores = Trabajador::whereNull('hora_vuelta')->get();

            if ($trabajadores->isEmpty()) {
                $bot->reply("⚠️ No hay recesos activos en este momento.");
            } else {
                $respuesta = "📋 Recesos activos:\n";
                foreach ($trabajadores as $trabajador) {
                    $respuesta .= "- *{$trabajador->nombre}* (DNI: {$trabajador->dni}) ingresó a las {$trabajador->hora_receso}\n";
                }
                $bot->reply($respuesta);
            }
        });

        // Fallback para preguntas fuera de contexto usando Gemini
        $botman->hears('{message}', function (BotMan $bot, $message) {
            $keywords = ['hola', 'registrar_visita', 'listar_visitas','registrar_receso', 'listar_recesos'];
            if (in_array(strtolower($message), $keywords)) {
                return; // No procesar, ya lo maneja un comando específico
            }
        
            $contexto = "Soy tu asistente virtual para el sistema de Registro de Visitas. Mis funciones incluyen: registrar visitas, listar visitas activas y registrar salidas.";
            $apiKey = 'AIzaSyDBwnLWYTmYQczbpck3CipgNWObRoIS5Y8'; // Reemplaza con tu API key válida
            $client = new Client();
        
            try {
                $response = $client->post('https://aistudio.google.com/app/prompts/1omOZO_967sxlfxgQhN6dPbuKV5VRzirp', [
                    'headers' => ['Content-Type' => 'application/json', 'Authorization' => "Bearer $apiKey"],
                    'json' => [
                        'prompt' => [
                            'context' => $contexto,
                            'messages' => [
                                ['content' => $message],
                            ],
                        ],
                        'temperature' => 0.7,
                        'top_k' => 40,
                        'top_p' => 0.9
                    ]
                ]);
        
                $data = json_decode($response->getBody()->getContents(), true);
        
                if (isset($data['candidates'][0]['content'])) {
                    $respuesta = $data['candidates'][0]['content'];
                    $bot->reply("🤖 {$respuesta}");
                } else {
                    $bot->reply("🤖 Lo siento, no pude generar una respuesta adecuada. Por favor, intenta nuevamente.");
                }
            } catch (RequestException $e) {
                $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 'sin código';
                $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'sin detalles';
                $bot->reply("🤖 Error al conectar con la IA: {$statusCode}. Detalles: {$errorBody}");
            } catch (\Exception $e) {
                $bot->reply("🤖 Hubo un problema: " . $e->getMessage());
            }
        });
        

        $botman->listen();
    }
}
