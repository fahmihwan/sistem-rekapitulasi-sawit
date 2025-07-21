<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LabaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OpsController;
use App\Http\Controllers\PabrikController;
use App\Http\Controllers\PenggajianController;
// use App\Http\Controllers\PenggajianV2Controller;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TbsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/auth', [AuthController::class, 'authenticated']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/periode', [PeriodeController::class, 'index']);
    Route::post('/periode', [PeriodeController::class, 'store']);
    Route::put('/periode/{id}', [PeriodeController::class, 'update']);
    Route::delete('/periode/{id}', [PeriodeController::class, 'destroy']);


    Route::get('/master/karyawan', [KaryawanController::class, 'index']);
    Route::post('/master/karyawan', [KaryawanController::class, 'store']);
    Route::put('/master/karyawan/{id}', [KaryawanController::class, 'update']);
    Route::delete('/master/karyawan/{id}', [KaryawanController::class, 'destroy']);

    Route::get('/master/pabrik', [PabrikController::class, 'index']);
    Route::post('/master/pabrik', [PabrikController::class, 'store']);
    Route::put('/master/pabrik/{id}', [PabrikController::class, 'update']);
    Route::delete('/master/pabrik/{id}', [PabrikController::class, 'destroy']);

    Route::get('/master/tarif', [TarifController::class, 'index']);
    Route::post('/master/tarif', [TarifController::class, 'store']);
    Route::put('/master/tarif/{id}', [TarifController::class, 'update']);
    Route::delete('/master/tarif/{id}', [TarifController::class, 'destroy']);

    Route::get('/master/ops', [OpsController::class, 'index']);
    Route::post('/master/ops', [OpsController::class, 'store']);
    Route::put('/master/ops/{id}', [OpsController::class, 'update']);
    Route::delete('/master/ops/{id}', [OpsController::class, 'destroy']);


    Route::get('/pembelian/tbs/{menu}/view', [TbsController::class, 'index']);
    Route::post('/pembelian/tbs/{menu}/view', [TbsController::class, 'store']);
    Route::put('/pembelian/tbs/{menu}/view/{id}', [TbsController::class, 'update']);
    Route::delete('/pembelian/tbs/{menu}/delete/{id}', [TbsController::class, 'destroy']);


    Route::get('/penjualan/tbs/{menu}/view', [PenjualanController::class, 'index']);
    Route::post('/penjualan/tbs/{menu}/view', [PenjualanController::class, 'store']);
    Route::put('/penjualan/tbs/{menu}/view/{id}', [PenjualanController::class, 'update']);
    Route::delete('/penjualan/tbs/{menu}/delete/{id}', [PenjualanController::class, 'destroy']);

    Route::get('/laba', [LabaController::class, 'index']);
    Route::get('/laba/{id}', [LabaController::class, 'detail']);

    Route::get('/slipgaji/karyawan', [SlipGajiController::class, 'index']);
    Route::get('/slipgaji/karyawan/{id}', [SlipGajiController::class, 'detail']);


    Route::get('/penggajian', [PenggajianController::class, 'index']);
    Route::get("penggajian/{id}", [PenggajianController::class, 'show_karyawan_penggajian_ajax']);
    Route::post('/penggajian', [PenggajianController::class, 'store']);
    Route::delete('/penggajian/{id}', [PenggajianController::class, 'destroy']);
    Route::get('/penggajian/{penggajianid}/{karyawanid}/detail-gaji', [PenggajianController::class, 'detail_gaji']);
    Route::get('/penggajian/{penggajianid}/{karyawanid}/ambil-gaji-perhari', [PenggajianController::class, 'ambil_gaji_perhari']);
    Route::put('/penggajian/{penggajianid}/{karyawanid}/ambil-gaji-perhari', [PenggajianController::class, 'update_ambil_gaji']);



    // Route::get('/penggajianv2', [PenggajianV2Controller::class, 'detail_gaji']);




    // Route::post('/master/karyawan', [SlipGajiController::class, 'store']);
    // Route::put('/master/karyawan/{id}', [SlipGajiController::class, 'update']);
    // Route::delete('/master/karyawan/{id}', [SlipGajiController::class, 'destroy']);

    Route::get('/pinjaman', [PinjamanController::class, 'index']);
    Route::post('/pinjaman', [PinjamanController::class, 'store']);
    Route::put('/pinjaman/{id}', [PinjamanController::class, 'update']);
    Route::delete('/pinjaman/{id}', [PinjamanController::class, 'destroy']);


    Route::get('/laporan/laporan-stock', [LaporanController::class, 'laporan_semua_stok']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
