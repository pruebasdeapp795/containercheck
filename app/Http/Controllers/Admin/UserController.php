<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['admin', 'personal', 'control_riesgo', 'monitoreo', 'despacho', 'comex', 'visualizador'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'usuario' => 'required|string|max:255|unique:users',
            'cedula' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:4|confirmed',
            'role' => ['required', Rule::in(['admin', 'personal', 'control_riesgo', 'monitoreo', 'despacho', 'comex', 'visualizador'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'usuario' => $request->usuario,
            'cedula' => $request->cedula,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = ['admin', 'personal', 'control_riesgo', 'monitoreo', 'despacho', 'comex', 'visualizador'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'usuario' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cedula' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:4|confirmed',
            'role' => ['required', Rule::in(['admin', 'personal', 'control_riesgo', 'monitoreo', 'despacho', 'comex', 'visualizador'])],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'usuario' => $request->usuario,
            'cedula' => $request->cedula,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
