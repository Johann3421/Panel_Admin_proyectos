<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitaField extends Model
{
    protected $fillable = ['label', 'name', 'type', 'options', 'required'];

}
