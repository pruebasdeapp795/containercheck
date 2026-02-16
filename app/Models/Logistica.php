<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logistica extends Model
{
    protected $table = 'logisticas';

    protected $fillable = [
        'tipo',
        'cantidad',
        'fecha_traslado',
        'user_id',
    ];

    protected $casts = [
        'fecha_traslado' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function precintos()
    {
        return $this->hasMany(Precinto::class);
    }
}
