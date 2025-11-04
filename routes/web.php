<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\PenginstalanController;



// Route::resource('dashboard', DashboardController::class);
Route::get('/', [DashboardController::class, 'dashboardAdmin'])->name('dashboard-admin');
Route::get('/pengguna/create-mahasiswa', [PenggunaController::class, 'createMahasiswa'])->name('pengguna.createMahasiswa');
Route::post('/pengguna/store-mahasiswa', [PenggunaController::class, 'storeMahasiswa'])->name('pengguna.storeMahasiswa');
Route::get('/pengguna/create-dosen', [PenggunaController::class, 'createDosen'])->name('pengguna.createDosen');
Route::post('/pengguna/store-dosen', [PenggunaController::class, 'storeDosen'])->name('pengguna.storeDosen');

Route::resource('pengguna', PenggunaController::class);
Route::resource('role', RoleController::class);
Route::resource('penginstalan', PenginstalanController::class);
Route::resource('perbaikan', PerbaikanController::class);
Route::resource('rekap', RekapController::class);

