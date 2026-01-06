<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\role;
use App\Models\login_log;
use Illuminate\Http\Request;

class LogLoginController extends Controller
{
    public function index(Request $request)
    {
        // bersihkan URL kalau filter hari ini
        if (
            !$request->filled('tanggal_filter') ||
            $request->tanggal_filter === 'hari_ini'
        ) {
            if ($request->query()) {
                return redirect()->route('logLogin.index');
            }
        }

        $query = login_log::with(['user.role', 'activities']);

        $tanggalFilter = $request->get('tanggal_filter', 'hari_ini');

        if ($tanggalFilter === 'hari_ini') {
            $query->whereDate('login_at', Carbon::today());
        } elseif ($tanggalFilter === 'kemarin') {
            $query->whereDate('login_at', Carbon::yesterday());
        } elseif ($tanggalFilter === 'besok') {
            $query->whereDate('login_at', Carbon::tomorrow());
        } elseif ($tanggalFilter === 'custom' && $request->filled('tanggal')) {
            $query->whereDate('login_at', $request->tanggal);
        }
        // kalau "semua" → tidak diberi whereDate

        // status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // role
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role_id', $request->role);
            });
        }

        // IP
        if ($request->filled('ip')) {
            $query->where('ip_address', $request->ip);
        }

        // search
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

        $ips = login_log::select('ip_address')
            ->distinct()
            ->orderBy('ip_address')
            ->pluck('ip_address');

        return view('admin.log_login.index', [
            'menu' => 'logLogin',
            'datauser' => $datauser,
            'roles' => $roles,
            'ips' => $ips,
            'tanggalFilter' => $tanggalFilter,
        ]);
    }

    public function show($id)
    {
        $log = login_log::with([
            'user.role',
            'activities' => function ($q) {
                $q->orderBy('created_at', 'asc'); // atau desc
            }
        ])->findOrFail($id);

        return view('admin.log_login.show', [
            'menu' => 'logLogin',
            'log' => $log,
        ]);
    }
}
