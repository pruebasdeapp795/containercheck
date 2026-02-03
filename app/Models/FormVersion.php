<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormVersion extends Model
{
    protected $fillable = ['version', 'is_active'];

    public function phases()
    {
        return $this->hasMany(Phase::class)->orderBy('order');
    }
}
