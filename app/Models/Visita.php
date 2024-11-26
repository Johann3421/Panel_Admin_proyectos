<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'fecha', 'dni', 'hora_ingreso', 'hora_salida', 'smotivo', 'tipopersona', 'lugar', 'nuevo_campo'];

}
