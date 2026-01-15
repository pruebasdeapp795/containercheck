<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PhaseController;
use App\Http\Controllers\Admin\FieldController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('phases', PhaseController::class);
    Route::resource('phases.fields', FieldController::class)->shallow();
});

Route::prefix('inspector')->name('inspector.')->middleware(['auth'])->group(function () {
    Route::resource('inspections', \App\Http\Controllers\Inspector\InspectionController::class);
    Route::post('inspections/{inspection}/sign', [
        \App\Http\Controllers\Inspector\SignatureController::class,
        'store'
    ])->name('inspections.sign');
});

Route::prefix('participant')->name('participant.')->group(function () {
    Route::post('login', [\App\Http\Controllers\Participant\AuthController::class, 'login'])->name('login');
    Route::middleware(['auth', 'role:participant'])->group(function () {
        Route::get('inspections', [\App\Http\Controllers\Participant\AuthController::class, 'inspections']);
        Route::post('inspections/{inspection}/sign', [\App\Http\Controllers\Inspector\SignatureController::class, 'store']);
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('pdf/blank', [\App\Http\Controllers\PDFController::class, 'downloadBlank'])->name('pdf.blank');
    Route::get('pdf/inspection/{id}', [
        \App\Http\Controllers\PDFController::class,
        'downloadInspection'
    ])->name('pdf.inspection');
});