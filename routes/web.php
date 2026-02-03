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

Route::middleware(['auth', 'role:control_riesgo'])->prefix('control-riesgo')->name('control-riesgo.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'dashboard'])->name('index');
    Route::get('/inspecciones/nueva/{version}', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'create'])->name('inspecciones.create');
    Route::get('/inspecciones/{response}/continuar', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'edit'])->name('inspecciones.edit');
    Route::post('/inspecciones/{response}/fase', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'savePhase'])->name('inspecciones.savePhase');
    Route::post('/inspecciones/{response}/finalizar', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'store'])->name('store');

    Route::get('/reportes', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'history'])->name('reportes');
    Route::get('/reportes/{response}', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'show'])->name('reportes.show');
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

    Route::prefix('admin/forms')->name('admin.forms.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\FormConfigController::class, 'index'])->name('index');
        Route::post('/version', [App\Http\Controllers\Admin\FormConfigController::class, 'storeVersion'])->name('version.store');
        Route::get('/{version}', [App\Http\Controllers\Admin\FormConfigController::class, 'show'])->name('show');
        Route::post('/{version}/activate', [App\Http\Controllers\Admin\FormConfigController::class, 'activate'])->name('activate');
        Route::post('/{version}/phase', [App\Http\Controllers\Admin\FormConfigController::class, 'storePhase'])->name('phase.store');
        Route::put('/phase/{phase}', [App\Http\Controllers\Admin\FormConfigController::class, 'updatePhase'])->name('phase.update');
        Route::post('/phase/{phase}/field', [App\Http\Controllers\Admin\FormConfigController::class, 'storeField'])->name('field.store');
        Route::put('/field/{field}', [App\Http\Controllers\Admin\FormConfigController::class, 'updateField'])->name('field.update');
        Route::post('/reorder-phases', [App\Http\Controllers\Admin\FormConfigController::class, 'reorderPhases'])->name('phases.reorder');
        Route::post('/reorder-fields', [App\Http\Controllers\Admin\FormConfigController::class, 'reorderFields'])->name('fields.reorder');
    });

    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/{response}', [App\Http\Controllers\Admin\ReportController::class, 'show'])->name('show');
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

