<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firma extends Model
{
    protected $table = 'signatures';

    protected $fillable = [
        'inspection_id',
        'user_id',
        'role',
        'image_path',
        'ip_address',
        'signed_at'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspeccion::class, 'inspection_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
