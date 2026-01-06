<?php

use App\Services\WhatsappService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LogLoginController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\PenginstalanController;

Route::fallback(function () {
    return response()->view('error.404', [], 404);
});
Route::get('/test-wa', function (App\Services\WhatsappService $wa) {
    $phone = '082285926175';
    $message = 'Test WA via Green API - ' . now();

    $sent = $wa->sendMessage($phone, $message);

    return response()->json([
        'sent' => $sent,
        'phone' => $phone,
        'timestamp' => now()->toDateTimeString()
    ]);
});


Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/authenticate', [LoginController::class, 'Auth'])->name('authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// SIGN UP
Route::get('/signup', [LoginController::class, 'signupForm'])->name('signup');
Route::post('/signup', [LoginController::class, 'signupStore'])->name('signup.store');


// Forgot Password - Step 1: Masukkan username
Route::get('/forgot-password', [LoginController::class, 'forgotPasswordForm'])
    ->name('forgot-password');

// Step 2: Kirim username → tampilkan pertanyaan keamanan
Route::post('/forgot-password/check-user', [LoginController::class, 'forgotCheckUser'])
    ->name('forgot-check-user');

// Step 3: Kirim jawaban keamanan → lanjut reset password
Route::post('/forgot-password/check-answer', [LoginController::class, 'forgotCheckAnswer'])
    ->name('forgot-check-answer');

// Step 4: Simpan password baru
Route::post('/forgot-password/reset', [LoginController::class, 'resetPassword'])
    ->name('forgot-reset');

Route::get('/forgot-password/reset/{id}', [LoginController::class, 'resetPasswordForm'])
    ->name('forgot-reset-form');

// add GET route so forgot-question dapat diakses langsung
Route::get('/forgot-password/question/{id}', [LoginController::class, 'forgotQuestionForm'])
    ->name('forgot-question');



Route::middleware('auth')->group(function () {

   
    Route::post('/chatbot', [ChatbotController::class, 'handle'])->name('chatbot.handle');

    Route::get('/my-profile', [LoginController::class, 'myProfile'])->middleware('auth')->name('my-profile');
    Route::put('/account/update', [LoginController::class, 'updateAccount'])->name('account.update');

    Route::get('/rekap/export/pdf', [RekapController::class, 'exportPdf'])->name('rekap.export.pdf');
    Route::get('/rekap/export/excel', [RekapController::class, 'exportExcel'])->name('rekap.export.excel');
    Route::get('/rekap/print', [RekapController::class, 'print'])->name('rekap.print');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    // About
    Route::get('/about', function () {
        return view('mahasiswa.about', [
            'menu'  => 'about',
            'title' => 'About',
        ]);
    })->name('about');

    // Contact
    Route::get('/contact', function () {
        return view('mahasiswa.contact', [
            'menu'  => 'contact',
            'title' => 'Contact',
        ]);
    })->name('contact');

    Route::post('/contact/submit', [ContactController::class, 'submit'])->name('mahasiswa.contact.submit');
    Route::get('/dashboard', [DashboardController::class, 'dashboardMahasiswa'])->name('dashboard-mahasiswa');
});

Route::middleware(['auth', 'role:dosen'])->group(function () {
    // About
    Route::get('/aboutt', function () {
        return view('dosen.about', [
            'menu'  => 'about',
            'title' => 'About',
        ]);
    })->name('aboutt');

    // Contact
    Route::get('/contactt', function () {
        return view('dosen.contact', [
            'menu'  => 'contact',
            'title' => 'Contact',
        ]);
    })->name('contactt');

    Route::post('/contact/submitt', [ContactController::class, 'submitDosen'])->name('dosen.contact.submit');
    Route::get('/Dashboard', [DashboardController::class, 'dashboardDosen'])->name('dashboard-dosen');
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::post('/contact/{id}/read', [ContactController::class, 'markAsRead'])->name('contact.read');

    Route::get('/log-login', [LogLoginController::class, 'index'])->name('logLogin.index');
    Route::get('/log-login/{id}', [LogLoginController::class, 'show'])->name('logLogin.show');

    Route::get('/dashboardAdmin', [DashboardController::class, 'dashboardAdmin'])->name('dashboard-admin');

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
    Route::patch('/penginstalan/{id}/status', [PenginstalanController::class, 'updateStatus'])
        ->name('penginstalan.updateStatus');

    Route::prefix('perbaikan')->group(function () {
        Route::get('/terhapus', [PerbaikanController::class, 'arsip'])->name('perbaikan.arsip');
        Route::patch('/pulihkan/{id}', [PerbaikanController::class, 'pulihkan'])->name('perbaikan.pulihkan');
        Route::delete('/hapus-semua', [PerbaikanController::class, 'hapusSemua'])->name('perbaikan.hapusSemua');
    });
    Route::patch('/perbaikan/{id}/status', [PerbaikanController::class, 'updateStatus'])
        ->name('perbaikan.updateStatus');

    Route::resource('perbaikan', PerbaikanController::class);

    Route::resource('penginstalan', PenginstalanController::class);

    Route::resource('software', SoftwareController::class);

    Route::resource('pengguna', PenggunaController::class);

    Route::resource('role', RoleController::class);

    Route::resource('rekap', RekapController::class);
});
