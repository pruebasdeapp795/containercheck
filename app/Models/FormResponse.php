<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormResponse extends Model
{
    protected $fillable = [
        'user_id',
        'form_version_id',
        'status',
        'last_phase_completed',
        'signature',
        'signed_at',
        'monitoreo_signature',
        'monitoreo_signed_at',
        'monitoreo_user_id',
        'rejection_reason'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'monitoreo_signed_at' => 'datetime',
    ];

    public function monitoreoUser()
    {
        return $this->belongsTo(User::class, 'monitoreo_user_id');
    }

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

    public function getFieldValue(string $label)
    {
        return $this->fieldResponses()
            ->whereHas('field', function ($query) use ($label) {
                $query->where('label', $label);
            })
            ->first()?->value;
    }

    public function getRejectionReason()
    {
        if ($this->rejection_reason) {
            return $this->rejection_reason;
        }

        // Try to find a field that has a rejection value matching its response
        $rejectedResponse = $this->fieldResponses()
            ->whereHas('field', function ($query) {
                $query->whereNotNull('rejection_value');
            })
            ->get()
            ->filter(function ($response) {
                return $response->value === $response->field->rejection_value;
            })
            ->first();

        if ($rejectedResponse) {
            return $rejectedResponse->field->label . ' Respuesta:' . $rejectedResponse->value;
        }

        return 'No especificado';
    }

    public function getContainerNumber()
    {
        return $this->getFieldValue('Numero de contenedor') ?? 'N/A';
    }
}
