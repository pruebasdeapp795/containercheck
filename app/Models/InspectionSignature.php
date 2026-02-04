<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionSignature extends Model
{
    protected $fillable = ['form_response_id', 'user_id', 'role_in_inspection', 'vest_number', 'signature', 'signed_at'];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formResponse()
    {
        return $this->belongsTo(FormResponse::class);
    }
}
