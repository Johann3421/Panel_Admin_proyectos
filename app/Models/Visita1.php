<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita1 extends Model
{
    use HasFactory;

    protected $table = 'visitas';
    protected $fillable = [
        'dni', 'nombre', 'tipopersona', 'nomoficina', 'smotivo',
        'lugar', 'fecha', 'hora_ingreso', 'hora_salida', 'observaciones'
    ];
}
