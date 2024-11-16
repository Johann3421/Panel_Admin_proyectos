<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni',
        'nombre',
        'tipopersona',
        'lugar',
        'smotivo',
        'hora_ingreso',
        'hora_salida',
        'fecha',
    ];
}
