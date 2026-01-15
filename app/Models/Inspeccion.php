<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspeccion extends Model
{
    protected $table = 'inspections';

    protected $fillable = [
        'inspector_id',
        'participant_id',
        'status',
        'completed_at',
        'expires_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function signatures()
    {
        return $this->hasMany(Firma::class, 'inspection_id');
    }
}
