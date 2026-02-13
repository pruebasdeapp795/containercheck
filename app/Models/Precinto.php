<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precinto extends Model
{
    protected $fillable = [
        'codigo',
        'tipo',
        'cantidad',
        'fecha_ingreso',
        'estado',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
    ];
}
