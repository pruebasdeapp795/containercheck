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
        if ($role === 'monitoreo') {
            return redirect()->route('monitoreo.index');
        }
        if ($role === 'visualizador') {
            return redirect()->route('visualizador.index');
        }
        if ($role === 'despacho') {
            return redirect()->route('despacho.index');
        }
        if ($role === 'comex') {
            return redirect()->route('comex.index');
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

// Shared API routes
Route::middleware(['auth', 'role:control_riesgo,despacho'])->group(function () {
    Route::get('/api/search-user/{cedula}', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'searchUserByCedula'])->name('api.searchUser');
});

Route::middleware(['auth', 'role:personal'])->group(function () {
    Route::get('/personal/index', function () {
        $user = Auth::user();
        $pendingSignatures = \App\Models\InspectionSignature::where('user_id', $user->id)
            ->whereNull('signed_at')
            ->with(['formResponse.formVersion', 'formResponse.user'])
            ->get();
        return view('personal.index', compact('pendingSignatures'));
    })->name('personal.index');

    Route::post('/personal/sign/{signature}', function (\Illuminate\Http\Request $request, \App\Models\InspectionSignature $signature) {
        if ($signature->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'signature_data' => 'required|string'
        ]);

        $signature->update([
            'signature' => $request->signature_data,
            'signed_at' => now()
        ]);

        return back()->with('success', 'Firma guardada correctamente.');
    })->name('personal.sign');
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
        Route::post('/{version}/duplicate', [App\Http\Controllers\Admin\FormConfigController::class, 'duplicate'])->name('duplicate');
        Route::post('/{version}/phase', [App\Http\Controllers\Admin\FormConfigController::class, 'storePhase'])->name('phase.store');
        Route::put('/phase/{phase}', [App\Http\Controllers\Admin\FormConfigController::class, 'updatePhase'])->name('phase.update');
        Route::delete('/phase/{phase}', [App\Http\Controllers\Admin\FormConfigController::class, 'deletePhase'])->name('phase.delete');
        Route::post('/phase/{phase}/field', [App\Http\Controllers\Admin\FormConfigController::class, 'storeField'])->name('field.store');
        Route::put('/field/{field}', [App\Http\Controllers\Admin\FormConfigController::class, 'updateField'])->name('field.update');
        Route::delete('/field/{field}', [App\Http\Controllers\Admin\FormConfigController::class, 'deleteField'])->name('field.delete');
        Route::post('/reorder-phases', [App\Http\Controllers\Admin\FormConfigController::class, 'reorderPhases'])->name('phases.reorder');
        Route::post('/reorder-fields', [App\Http\Controllers\Admin\FormConfigController::class, 'reorderFields'])->name('fields.reorder');
    });

    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/{response}', [App\Http\Controllers\Admin\ReportController::class, 'show'])->name('show');
    });
});

Route::middleware(['auth', 'role:monitoreo'])->prefix('monitoreo')->name('monitoreo.')->group(function () {
    Route::get('/index', [\App\Http\Controllers\MonitoreoController::class, 'index'])->name('index');
    Route::get('/inspeccion/{response}', [\App\Http\Controllers\MonitoreoController::class, 'show'])->name('show');
    Route::post('/inspeccion/{response}/liberar', [\App\Http\Controllers\MonitoreoController::class, 'liberate'])->name('liberate');
    Route::post('/inspeccion/{response}/rechazar', [\App\Http\Controllers\MonitoreoController::class, 'reject'])->name('reject');
    Route::post('/firma/cargar', [\App\Http\Controllers\MonitoreoController::class, 'uploadSignature'])->name('uploadSignature');
});

Route::middleware(['auth', 'role:visualizador'])->prefix('visualizador')->name('visualizador.')->group(function () {
    Route::get('/index', [\App\Http\Controllers\MonitoreoController::class, 'indexViewer'])->name('index');
    Route::get('/inspeccion/{response}', [App\Http\Controllers\MonitoreoController::class, 'showViewer'])->name('show');
    Route::get('/inventario', [App\Http\Controllers\MonitoreoController::class, 'inventario'])->name('inventario');
    Route::post('/inventario', [App\Http\Controllers\MonitoreoController::class, 'storePrecinto'])->name('inventario.store');
    Route::post('/inventario/trasladar', [\App\Http\Controllers\MonitoreoController::class, 'trasladarALogistica'])->name('inventario.trasladar');
});

Route::middleware(['auth', 'role:despacho'])->prefix('despacho')->name('despacho.')->group(function () {
    Route::get('/index', [App\Http\Controllers\Despacho\DespachoController::class, 'index'])->name('index');
    Route::get('/inspecciones/nueva/{version}', [App\Http\Controllers\Despacho\DespachoController::class, 'create'])->name('inspecciones.create');
    Route::get('/inspecciones/{response}/continuar', [App\Http\Controllers\Despacho\DespachoController::class, 'edit'])->name('inspecciones.edit');
    Route::post('/inspecciones/{response}/fase', [App\Http\Controllers\Despacho\DespachoController::class, 'savePhase'])->name('inspecciones.savePhase');
    Route::post('/inspecciones/{response}/finalizar', [App\Http\Controllers\Despacho\DespachoController::class, 'store'])->name('store');

    // Inventory routes
    Route::get('/inventario', [App\Http\Controllers\Despacho\DespachoController::class, 'inventario'])->name('inventario');
    Route::post('/inventario', [App\Http\Controllers\Despacho\DespachoController::class, 'storePrecinto'])->name('inventario.store');

    // Report routes
    Route::get('/reportes', [App\Http\Controllers\Despacho\DespachoController::class, 'history'])->name('reportes');
    Route::get('/reportes/{response}', [App\Http\Controllers\Despacho\DespachoController::class, 'show'])->name('show');
});

Route::middleware(['auth', 'role:comex'])->prefix('comex')->name('comex.')->group(function () {
    Route::get('/index', [\App\Http\Controllers\ComexController::class, 'index'])->name('index');
    Route::get('/export/csv', [\App\Http\Controllers\ComexController::class, 'exportCsv'])->name('export.csv');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// API route for searching precintos
Route::middleware('auth')->get('/api/search-precinto', [\App\Http\Controllers\MonitoreoController::class, 'searchPrecinto'])->name('api.searchPrecinto');

// Public route for inspection photos (Gallery)
Route::get('/galeria/inspeccion/{response}', [App\Http\Controllers\ControlRiesgo\FormResponseController::class, 'gallery'])->name('reportes.gallery');

