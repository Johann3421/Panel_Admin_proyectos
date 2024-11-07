<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';
    protected $fillable = [
        'nombre', 'dni', 'hora_receso', 'hora_vuelta', 'duracion'
    ];
}
