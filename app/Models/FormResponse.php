<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormResponse extends Model
{
    protected $fillable = ['user_id', 'form_version_id', 'status', 'last_phase_completed', 'signature', 'signed_at'];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formVersion()
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function fieldResponses()
    {
        return $this->hasMany(FieldResponse::class);
    }

    public function inspectionSignatures()
    {
        return $this->hasMany(InspectionSignature::class);
    }
}
