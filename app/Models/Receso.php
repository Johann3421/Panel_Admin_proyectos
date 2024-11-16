<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receso extends Model
{
    // Nombre de la tabla asociada (si no sigue la convención plural de Eloquent)
    protected $table = 'recesos';

    // Columnas permitidas para asignación masiva
    protected $fillable = [
        'nombre',       // Nombre del trabajador
        'dni',          // Documento de identidad
        'duracion',     // Duración del receso en minutos
        'hora_receso',  // Hora en la que comenzó el receso
        'hora_vuelta',  // Hora en la que terminó el receso
        'estado',       // Estado del receso (activo, finalizado)
    ];

    // Opcional: Desactivar timestamps si no estás usando columnas created_at/updated_at
    public $timestamps = false;
}
