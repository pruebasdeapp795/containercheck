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
        'logistica_id',
        'form_response_id',
        'usado_at',
        'numero_contenedor',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'usado_at' => 'datetime',
    ];

    public function logistica()
    {
        return $this->belongsTo(Logistica::class);
    }

    public function formResponse()
    {
        return $this->belongsTo(FormResponse::class);
    }
}
