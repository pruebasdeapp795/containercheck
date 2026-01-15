<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    protected $table = 'phases';

    protected $fillable = ['name', 'table_name', 'order', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fields()
    {
        return $this->hasMany(Field::class, 'phase_id')->orderBy('order');
    }
}
