<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    protected $fillable = ['form_version_id', 'name', 'is_visible', 'order'];

    public function formVersion()
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class)->orderBy('order');
    }
}
