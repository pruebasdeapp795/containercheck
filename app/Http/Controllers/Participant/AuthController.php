<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Inspeccion;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|string',
        ]);

        $user = User::where('document_number', $validated['document_number'])
            ->where('role', 'participant')
            ->first();

        // Password is same as document number for participants
        if ($user && \Illuminate\Support\Facades\Hash::check($validated['document_number'], $user->password)) {
            Auth::login($user);
            return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $user->createToken('auth_token')->plainTextToken]); // Assuming Sanctum or just session
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function inspections()
    {
        $user = auth()->user();
        $inspections = Inspeccion::where('participant_id', $user->id)
            ->whereIn('status', ['pending_signature', 'in_progress']) // Participant can view pending or in progress if assigned
            ->get();

        return response()->json($inspections);
    }
}
