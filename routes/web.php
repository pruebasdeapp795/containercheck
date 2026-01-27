<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
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

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

