<?php

use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PabrikController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('pages.dashboard');
});

// Route::get("/karyawans", )
Route::get('/master/karyawan', [KaryawanController::class, 'index']);
Route::post('/master/karyawan', [KaryawanController::class, 'store']);
Route::put('/master/karyawan/{id}', [KaryawanController::class, 'update']);
Route::delete('/master/karyawan/{id}', [KaryawanController::class, 'destroy']);

Route::get('/master/pabrik', [PabrikController::class, 'index']);
Route::post('/master/pabrik', [PabrikController::class, 'store']);
Route::put('/master/pabrik/{id}', [PabrikController::class, 'update']);
Route::delete('/master/pabrik/{id}', [PabrikController::class, 'destroy']);
