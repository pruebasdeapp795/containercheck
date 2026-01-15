<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = ['phase_id', 'label', 'column_name', 'description', 'type', 'required', 'options', 'conditionals', 'order'];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
        'conditionals' => 'array',
    ];

    public function phase()
    {
        return $this->belongsTo(Fase::class, 'phase_id');
    }
}
