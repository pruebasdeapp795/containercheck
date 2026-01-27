<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showPersonalLogin()
    {
        return view('auth.personal.login');
    }

    public function personalLogin(Request $request)
    {
        $credentials = $request->validate([
            'cedula' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['cedula' => $credentials['cedula'], 'password' => $credentials['password'], 'role' => 'personal'])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['cedula' => 'Cédula o contraseña incorrectas.']);
    }

    public function showControlRiegoLogin()
    {
        return view('auth.control-riesgo.login');
    }

    public function controlRiegoLogin(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => 'required',
            'password' => 'required',
        ]);

        // Intentar autenticar por usuario en lugar de email
        if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password'], 'role' => 'control_riesgo'])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['usuario' => 'Credenciales incorrectas para Control Riego.']);
    }

    public function showAdminLogin()
    {
        return view('auth.admin.login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password'], 'role' => 'admin'])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['usuario' => 'Credenciales incorrectas para Administrador.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/portal');
    }
}
