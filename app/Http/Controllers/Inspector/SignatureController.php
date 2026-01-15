<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inspeccion;
use App\Models\Firma;

class SignatureController extends Controller
{
    public function store(Request $request, $inspectionId)
    {
        $validated = $request->validate([
            'role' => 'required|in:inspector,participant',
            'image_path' => 'required|string', // Base64 or path
        ]);

        $inspection = Inspeccion::findOrFail($inspectionId);

        // Security check: inspector can only sign their own inspections
        if ($validated['role'] === 'inspector' && $inspection->inspector_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Participant check logic would go here (e.g. check current user is participant)

        $signature = Firma::create([
            'inspection_id' => $inspection->id,
            'user_id' => auth()->id(),
            'role' => $validated['role'],
            'image_path' => $validated['image_path'], // In real app, save base64 to storage first
            'ip_address' => $request->ip(),
            'signed_at' => now(),
        ]);

        // Update inspection status if needed
        if ($validated['role'] === 'inspector') {
            // Logic: if inspector signs, maybe status changes to pending_signature (for participant)?
            $inspection->status = 'pending_signature';
            $inspection->save();
        }

        return response()->json($signature, 201);
    }
}
