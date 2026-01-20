<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogActivitiesController extends Controller
{
    public function index(Request $request)
    {
        // Jika ada query string yang hanya berisi tanggal_filter=hari_ini,
        // redirect ke route tanpa query supaya URL bersih.
        $onlyTanggalFilterHariIni = $request->query() === ['tanggal_filter' => 'hari_ini'];
        if ($onlyTanggalFilterHariIni) {
            return redirect()->route('logAktif.index');
        }

        $query = UserActivity::with(['user', 'loginLog']);

        // ----- FILTER TANGGAL (prioritas) -----
        // Pastikan hanya satu kondisi whereDate yang dipakai
        if ($request->filled('tanggal_filter')) {
            if ($request->tanggal_filter === 'hari_ini') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($request->tanggal_filter === 'kemarin') {
                $query->whereDate('created_at', Carbon::yesterday());
            } elseif ($request->tanggal_filter === 'custom') {
                // jika custom tapi tanggal kosong -> abaikan (tampil hari ini)
                if ($request->filled('tanggal')) {
                    // normalisasi format tanggal (YYYY-MM-DD)
                    $date = Carbon::createFromFormat('Y-m-d', $request->tanggal)->toDateString();
                    $query->whereDate('created_at', $date);
                } else {
                    // optional: kalau ingin memaksa input tanggal wajib saat custom,
                    // bisa redirect balik dengan pesan error. Untuk sekarang: tampilkan hari ini.
                    $query->whereDate('created_at', Carbon::today());
                }
            } else {
                // unknown value: default hari ini
                $query->whereDate('created_at', Carbon::today());
            }
        } else {
            // default = hari ini
            $query->whereDate('created_at', Carbon::today());
        }

        // ----- FILTER LAIN (user, type, status) -----
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->whereHas('loginLog', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // pagination & keep query
        $activities = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.log_aktivitas.index', [
            'menu' => 'logAktif',
            'activities'    => $activities,
            'users'         => User::orderBy('nama')->get(),
            'tanggalFilter' => $request->tanggal_filter ?? 'hari_ini',
        ]);
    }
}
