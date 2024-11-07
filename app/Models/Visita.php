<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni', 'nombre', 'tipopersona', 'fecha', 'hora_ingreso', 'hora_salida', 'smotivo', 'lugar'
    ];

    public $timestamps = false; // Desactiva los timestamps automáticos si no están en la tabla
}
