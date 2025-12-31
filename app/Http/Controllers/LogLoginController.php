<?php

namespace App\Http\Controllers;

use App\Models\login_log;
use App\Models\role;
use Illuminate\Http\Request;

class LogLoginController extends Controller
{
    public function index(Request $request)
    {
        $query = login_log::with(['user.role']);

        // filter tanggal login
        if ($request->filled('tanggal')) {
            $query->whereDate('login_at', $request->tanggal);
        }
        // filter status online / offline
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // filter role
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role_id', $request->role);
            });
        }

        // filter IP
        if ($request->filled('ip')) {
            $query->where('ip_address', $request->ip);
        }

        // search nama / username
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        $datauser = $query
            ->orderByDesc('login_at')
            ->paginate(10)
            ->withQueryString();

        $roles = role::all();

        // ambil semua IP unik untuk dropdown
        $ips = login_log::select('ip_address')
            ->distinct()
            ->orderBy('ip_address')
            ->pluck('ip_address');

        return view('admin.log_login', [
            'menu' => 'logLogin',
            'datauser' => $datauser,
            'roles' => $roles,
            'ips' => $ips,
        ]);
    }
}
