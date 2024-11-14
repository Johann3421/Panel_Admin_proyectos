<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecesoField extends Model
{
    protected $fillable = ['label', 'name', 'type', 'options', 'required'];
}
