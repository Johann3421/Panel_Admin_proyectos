<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Visita;
use App\Models\Receso;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                    Button::create('Registrar receso')->value('registrar_receso'),
                    Button::create('Listar recesos activos')->value('listar_recesos'),
                ]);

            $bot->reply($question);
        });

        // Registrar visita
        $botman->hears('registrar_visita|quiero registrar una visita|registrar visita|nueva visita', function (BotMan $bot) {
            $bot->reply("Por favor, proporciona los datos de la visita separados por comas:\n`DNI, Nombre, Tipo de Persona, Lugar, Motivo`");
        });

        $botman->hears('{dni},{nombre},{tipopersona},{lugar},{smotivo}', function (BotMan $bot, $dni, $nombre, $tipopersona, $lugar, $smotivo) {
            if (empty($dni) || empty($nombre) || empty($tipopersona) || empty($lugar) || empty($smotivo)) {
                $bot->reply("❌ Datos incompletos. Asegúrate de enviar los datos en el formato correcto: `DNI, Nombre, Tipo de Persona, Lugar, Motivo`.");
                return;
            }

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

        $botman->hears('listar_visitas|listar visitas|visitas activas', function (BotMan $bot) {
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

        // Registrar receso
        $botman->hears('registrar_receso|quiero registrar un receso|registrar receso|nuevo receso', function (BotMan $bot) {
            $bot->reply("Proporciona los datos del receso separados por comas:\n`ID Trabajador, Duración (en minutos)`");
        });

        $botman->hears('{workerId},{duracion}', function (BotMan $bot, $workerId, $duracion) {
            $horaReceso = Carbon::now('America/Lima');

            $recesoActivo = DB::table('recesos')
                ->where('trabajador_id', $workerId)
                ->where('estado', 'activo')
                ->first();

            if ($recesoActivo) {
                $bot->reply("⚠️ Ya hay un receso activo para este trabajador.");
                return;
            }

            $trabajador = DB::table('trabajadores')
                ->select('nombre', 'dni')
                ->where('id', $workerId)
                ->first();

            if (!$trabajador) {
                $bot->reply("❌ Trabajador no encontrado con ID: {$workerId}.");
                return;
            }

            DB::table('trabajadores')->where('id', $workerId)->update([
                'hora_receso' => $horaReceso,
                'duracion' => $duracion,
                'hora_vuelta' => null
            ]);

            DB::table('recesos')->insert([
                'trabajador_id' => $workerId,
                'nombre' => $trabajador->nombre,
                'dni' => $trabajador->dni,
                'duracion' => $duracion,
                'hora_receso' => $horaReceso,
                'estado' => 'activo'
            ]);

            $bot->reply("✅ Receso registrado para *{$trabajador->nombre}* con duración de {$duracion} minutos.");
        });

        $botman->hears('listar_recesos|listar recesos|recesos activos', function (BotMan $bot) {
            $recesosActivos = DB::table('recesos')
                ->where('estado', 'activo')
                ->get();

            if ($recesosActivos->isEmpty()) {
                $bot->reply("⚠️ No hay recesos activos en este momento.");
            } else {
                $respuesta = "📋 Recesos activos:\n";
                foreach ($recesosActivos as $receso) {
                    $respuesta .= "- *{$receso->nombre}* (DNI: {$receso->dni}), comenzó a las {$receso->hora_receso}, duración: {$receso->duracion} minutos.\n";
                }
                $bot->reply($respuesta);
            }
        });

        // Fallback
        $botman->fallback(function (BotMan $bot) {
            $bot->reply("🤖 Lo siento, no entendí eso. Por favor, intenta con algo como:\n- 'Registrar visita'\n- 'Listar visitas activas'\n- 'Registrar receso'\n- 'Listar recesos activos'.");
        });

        $botman->listen();
    }
    
}
