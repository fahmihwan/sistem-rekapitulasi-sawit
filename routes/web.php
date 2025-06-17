<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PabrikController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\TbsController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index']);

// Route::get("/karyawans", )
Route::get('/master/karyawan', [KaryawanController::class, 'index']);
Route::post('/master/karyawan', [KaryawanController::class, 'store']);
Route::put('/master/karyawan/{id}', [KaryawanController::class, 'update']);
Route::delete('/master/karyawan/{id}', [KaryawanController::class, 'destroy']);

Route::get('/master/pabrik', [PabrikController::class, 'index']);
Route::post('/master/pabrik', [PabrikController::class, 'store']);
Route::put('/master/pabrik/{id}', [PabrikController::class, 'update']);
Route::delete('/master/pabrik/{id}', [PabrikController::class, 'destroy']);


Route::get('/pembelian/tbs/{menu}/view', [TbsController::class, 'index']);
Route::post('/pembelian/tbs/{menu}/view', [TbsController::class, 'store']);
Route::put('/pembelian/tbs/{menu}/view/{id}', [TbsController::class, 'update']);
Route::delete('/pembelian/tbs/{menu}/delete/{id}', [TbsController::class, 'destroy']);


Route::get('/penjualan/tbs/{menu}/view', [PenjualanController::class, 'index']);
Route::post('/penjualan/tbs/{menu}/view', [PenjualanController::class, 'store']);
Route::put('/penjualan/tbs/{menu}/view/{id}', [PenjualanController::class, 'update']);
Route::delete('/penjualan/tbs/{menu}/delete/{id}', [PenjualanController::class, 'destroy']);


Route::get('/laporan/laporan-stock', [LaporanController::class, 'laporan_stock']);
