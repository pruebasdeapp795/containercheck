<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'control_riesgo') {
            return redirect()->route('control-riesgo.index');
        }
        if ($role === 'personal') {
            return redirect()->route('personal.index');
        }
        if ($role === 'admin') {
            return redirect()->route('admin.index');
        }
    }
    return view('portal.index');
})->name('portal');

Route::prefix('login')->group(function () {
    Route::get('/personal', [AuthController::class, 'showPersonalLogin'])->name('login.personal');
    Route::post('/personal', [AuthController::class, 'personalLogin']);

    Route::get('/control-riesgo', [AuthController::class, 'showControlRiegoLogin'])->name('login.control-riesgo');
    Route::post('/control-riesgo', [AuthController::class, 'controlRiegoLogin']);

    Route::get('/admin', [AuthController::class, 'showAdminLogin'])->name('login.admin');
    Route::post('/admin', [AuthController::class, 'adminLogin']);
});

Route::middleware(['auth', 'role:control_riesgo'])->group(function () {
    Route::get('/control-riesgo/index', function () {
        return view('control-riesgo.index');
    })->name('control-riesgo.index');
});

Route::middleware(['auth', 'role:personal'])->group(function () {
    Route::get('/personal/index', function () {
        return view('personal.index');
    })->name('personal.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/index', function () {
        return view('admin.index');
    })->name('admin.index');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

