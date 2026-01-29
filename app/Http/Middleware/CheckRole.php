<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $allowed = array_map('trim', explode(',', $roles));
        $userRole = $user->role->status ?? null;

        /*
        =========================
        CEK MAINTENANCE DI SINI
        =========================
        Berlaku hanya untuk mahasiswa & dosen
        Admin tetap bisa masuk
        */
        $maintenance = Maintenance::active();

        if ($maintenance && in_array($userRole, ['mahasiswa', 'dosen'])) {

            Auth::logout();

            // hitung sisa detik
            $seconds = now()->diffInSeconds($maintenance->ends_at, false);

            // kalau sudah lewat (negatif) set 0
            $seconds = max(0, $seconds);

            $h = floor($seconds / 3600);
            $m = floor(($seconds % 3600) / 60);
            $s = $seconds % 60;

            // buat teks waktu manusia
            if ($h > 0) {
                $waktu = "{$h} jam {$m} menit";
            } elseif ($m > 0) {
                $waktu = "{$m} menit {$s} detik";
            } else {
                $waktu = "{$s} detik";
            }

            return redirect()->route('login')->with(
                'maintenance_warning',
                "Sistem sedang maintenance, coba lagi dalam {$waktu}."
            );
        }


        /*
        =========================
        CEK ROLE (ASLI PUNYA ANDA)
        =========================
        */
        if (!in_array($userRole, $allowed)) {

            if ($userRole === 'admin') {
                return redirect()->route('dashboard-admin');
            } elseif ($userRole === 'dosen') {
                return redirect()->route('dashboard-dosen');
            } elseif ($userRole === 'mahasiswa') {
                return redirect()->route('dashboard-mahasiswa');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
