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
            return redirect()->intended(route('personal.index'));
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

        if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            // Verify allowed roles for this portal
            $allowedRoles = ['control_riesgo', 'monitoreo', 'visualizador', 'despacho', 'comex'];

            if (in_array($user->role, $allowedRoles)) {
                $request->session()->regenerate();

                // Redirect based on role
                if ($user->role === 'monitoreo') {
                    return redirect()->intended(route('monitoreo.index'));
                } elseif ($user->role === 'visualizador') {
                    return redirect()->intended(route('visualizador.index'));
                } elseif ($user->role === 'despacho') {
                    return redirect()->intended(route('despacho.index'));
                } elseif ($user->role === 'comex') {
                    return redirect()->intended(route('comex.index'));
                } else {
                    return redirect()->intended(route('control-riesgo.index'));
                }
            } else {
                Auth::logout();
                return back()->withErrors(['usuario' => 'Este usuario no tiene acceso a Control Riesgo.']);
            }
        }

        return back()->withErrors(['usuario' => 'Credenciales incorrectas.']);
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
            return redirect()->intended(route('admin.index'));
        }

        return back()->withErrors(['usuario' => 'Credenciales incorrectas para Administrador.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal');
    }
}
