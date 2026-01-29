<?php

namespace App\Http\Controllers;

use App\Models\login_log;
use App\Models\Maintenance;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index()
    {
        $active = Maintenance::active();
        return view('auth.maintenance', [
            'menu' => 'maintenance',
            'active' => $active
            ]);
    }

    public function start(Request $request)
    {
        $request->validate([
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'reason' => 'nullable|string'
        ]);

        Maintenance::where('is_active', true)->update(['is_active' => false]);

        // 🔥 PENTING — CAST KE INTEGER
        $minutes = (int) $request->duration_minutes;

        $endsAt = now()->addMinutes($minutes);

        Maintenance::create([
            'is_active' => true,
            'ends_at' => $endsAt,
            'reason' => $request->reason,
            'created_by' => auth::id()
        ]);

        $authUser = Auth::user();

        if ($authUser) {
            $loginLog = login_log::where('user_id', $authUser->id)
                ->where('status', 'online')
                ->latest('login_at')
                ->first();

            if ($loginLog) {
                UserActivity::create([
                    'user_id'      => $authUser->id,
                    'login_log_id' => $loginLog->id,
                    'activity'     => 'Mengubah sistem menjadi Maintenance',
                    'type' => 'sistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        return back()->with('success', 'Maintenance aktif sampai ' . $endsAt);
    }


    public function stop()
    {
        Maintenance::where('is_active', true)->update([
            'is_active' => false,
            'ends_at' => now()
        ]);

        $authUser = Auth::user();

        if ($authUser) {
            $loginLog = login_log::where('user_id', $authUser->id)
                ->where('status', 'online')
                ->latest('login_at')
                ->first();

            if ($loginLog) {
                UserActivity::create([
                    'user_id'      => $authUser->id,
                    'login_log_id' => $loginLog->id,
                    'activity'     => 'Menonaktifkan sistem Maintenance',
                    'type' => 'sistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        return back()->with('success', 'Maintenance dihentikan');
    }
}
