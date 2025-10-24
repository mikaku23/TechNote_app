<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenginstalanController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\RekapController;



// Route::resource('dashboard', DashboardController::class);
Route::get('/', [DashboardController::class, 'dashboardAdmin'])->name('dashboard-admin');
Route::resource('pengguna', PenggunaController::class);
Route::resource('penginstalan', PenginstalanController::class);
Route::resource('perbaikan', PerbaikanController::class);
Route::resource('rekap', RekapController::class);

