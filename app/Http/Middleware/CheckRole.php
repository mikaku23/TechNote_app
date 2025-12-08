<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $roles)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $allowed = array_map('trim', explode(',', $roles));
        $userRole = $user->role->status ?? null;

        if (! in_array($userRole, $allowed)) {
            // jika mau redirect ke dashboard sesuai role user:
            if ($userRole === 'admin') {
                return redirect()->route('dashboard-admin');
            } elseif ($userRole === 'dosen') {
                return redirect()->route('dashboard-dosen');
            } elseif ($userRole === 'mahasiswa') {
                return redirect()->route('dashboard-mahasiswa');
            }

            // atau jika ingin 403:
            // abort(403);
            return redirect()->route('login');
        }

        return $next($request);
    }
}
