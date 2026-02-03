<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldResponse extends Model
{
    protected $fillable = ['form_response_id', 'field_id', 'value'];

    public function formResponse()
    {
        return $this->belongsTo(FormResponse::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
