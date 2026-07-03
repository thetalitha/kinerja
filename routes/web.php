<?php

use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\PenilaianController;
use App\Http\Controllers\Admin\PesertaController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginProses'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ===== ADMIN ROUTES =====
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
    Route::get('/peserta', [PesertaController::class, 'index'])->name('peserta.index');
    Route::get('/peserta/{id}/detail', [PesertaController::class, 'detail'])->name('peserta.detail');

    //kriteria
    Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
    Route::post('/kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
    Route::post('/kriteria/{id}/sub', [KriteriaController::class, 'storeSubKriteria'])->name('kriteria.sub.store');
    Route::put('/kriteria/sub/{id}', [KriteriaController::class, 'updateSub'])->name('kriteria.sub.update');
    Route::delete('/kriteria/sub/{id}', [KriteriaController::class, 'destroySub'])->name('kriteria.sub.destroy');
    Route::put('/kriteria/{id}', [KriteriaController::class, 'update'])->name('kriteria.update');
    Route::delete('/kriteria/{id}', [KriteriaController::class, 'destroy'])->name('kriteria.destroy');

    //penilaian
    Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::post('/penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');
    Route::get('/penilaian/{userId}/get', [PenilaianController::class, 'getPenilaian'])->name('penilaian.get');
    Route::get('/penilaian/{userId}/sync-logbook', [PenilaianController::class, 'getSyncLogbook'])->name('penilaian.sync.logbook');

});

// ===== MENTOR ROUTES =====
Route::middleware(['auth', 'role:mentor'])->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('/dashboard', function () { return view('mentor.dashboard'); })->name('dashboard');

});