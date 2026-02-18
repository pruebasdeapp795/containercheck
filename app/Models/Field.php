<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = ['phase_id', 'label', 'type', 'options', 'rejection_value', 'is_required', 'is_precinto', 'is_visible', 'order'];

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function responses()
    {
        return $this->hasMany(FieldResponse::class);
    }
}
