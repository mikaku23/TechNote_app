<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\PenginstalanController;



// Route::resource('dashboard', DashboardController::class);
Route::get('/', [DashboardController::class, 'dashboardAdmin'])->name('dashboard-admin');
Route::get('/pengguna/create-mahasiswa', [PenggunaController::class, 'createMahasiswa'])->name('pengguna.createMahasiswa');
Route::post('/pengguna/store-mahasiswa', [PenggunaController::class, 'storeMahasiswa'])->name('pengguna.storeMahasiswa');
Route::get('/pengguna/create-dosen', [PenggunaController::class, 'createDosen'])->name('pengguna.createDosen');
Route::post('/pengguna/store-dosen', [PenggunaController::class, 'storeDosen'])->name('pengguna.storeDosen');
Route::delete('/pengguna/hapus-semua', [PenggunaController::class, 'hapusSemua'])->name('pengguna.hapusSemua');

Route::delete('/software/hapus-semua', [SoftwareController::class, 'hapusSemua'])->name('software.hapusSemua');

Route::delete('/penginstalan/hapus-semua', [PenginstalanController::class, 'hapusSemua'])->name('penginstalan.hapusSemua');
Route::get('/penginstalan/arsip', [PenginstalanController::class, 'arsip'])
    ->name('penginstalan.terhapus');
Route::patch('/penginstalan/pulihkan/{id}', [PenginstalanController::class, 'pulihkan'])
    ->name('penginstalan.pulihkan');

Route::prefix('perbaikan')->group(function () {
    Route::get('/terhapus', [PerbaikanController::class, 'arsip'])->name('perbaikan.arsip');
    Route::patch('/pulihkan/{id}', [PerbaikanController::class, 'pulihkan'])->name('perbaikan.pulihkan');
    Route::delete('/hapus-semua', [PerbaikanController::class, 'hapusSemua'])->name('perbaikan.hapusSemua');
});
Route::patch('/perbaikan/{id}/status', [PerbaikanController::class, 'updateStatus'])
    ->name('perbaikan.updateStatus');

Route::resource('pengguna', PenggunaController::class);
Route::resource('software', SoftwareController::class);
Route::resource('role', RoleController::class);
Route::resource('penginstalan', PenginstalanController::class);
Route::resource('perbaikan', PerbaikanController::class);

Route::resource('rekap', RekapController::class);

