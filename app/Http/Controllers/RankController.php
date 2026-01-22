<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;

class RankController extends Controller
{
    public function index(Request $request)
    {
        $type     = $request->query('type', 'mahasiswa');
        $semester = $request->query('semester', 'ini'); // ini | kemarin
        $limit    = 10;

        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;

        // tentukan semester saat ini
        $currentSemester = $month <= 6 ? 1 : 2;

        // validasi semester kemarin
        if ($semester === 'kemarin' && $currentSemester === 1) {
            $semester = 'ini';
        }

        // tentukan range bulan
        if ($semester === 'ini') {
            if ($currentSemester === 1) {
                $startMonth = 1;
                $endMonth   = 6;
            } else {
                $startMonth = 7;
                $endMonth   = 12;
            }
        } else {
            // semester kemarin (pasti semester 2 → ambil semester 1)
            $startMonth = 1;
            $endMonth   = 6;
        }

        if ($type === 'dosen') {
            $top = User::with('role')
                ->withCount([
                    'perbaikans as perbaikans_count' => function ($q) use ($year, $startMonth, $endMonth) {
                        $q->whereYear('created_at', $year)
                            ->whereMonth('created_at', '>=', $startMonth)
                            ->whereMonth('created_at', '<=', $endMonth);
                    }
                ])
                ->withMin([
                    'perbaikans as first_perbaikan_at' => function ($q) use ($year, $startMonth, $endMonth) {
                        $q->whereYear('created_at', $year)
                            ->whereMonth('created_at', '>=', $startMonth)
                            ->whereMonth('created_at', '<=', $endMonth);
                    }
                ], 'created_at')
                ->whereHas('role', fn($q) => $q->where('status', 'dosen'))
                ->having('perbaikans_count', '>', 0)
                ->orderByDesc('perbaikans_count')
                ->orderBy('first_perbaikan_at') // 🔑 tie breaker
                ->limit($limit)
                ->get();


            $labels = $top->pluck('nama')->map(
                fn($n) =>
                strlen($n) > 20 ? substr($n, 0, 17) . '...' : $n
            )->values()->toArray();

            $data = $top->pluck('perbaikans_count')->values()->toArray();
        } else {
            $top = User::with('role')
                ->withCount([
                    'penginstalans as penginstalans_count' => function ($q) use ($year, $startMonth, $endMonth) {
                        $q->whereYear('created_at', $year)
                            ->whereMonth('created_at', '>=', $startMonth)
                            ->whereMonth('created_at', '<=', $endMonth);
                    }
                ])
                ->withMin([
                    'penginstalans as first_penginstalan_at' => function ($q) use ($year, $startMonth, $endMonth) {
                        $q->whereYear('created_at', $year)
                            ->whereMonth('created_at', '>=', $startMonth)
                            ->whereMonth('created_at', '<=', $endMonth);
                    }
                ], 'created_at')
                ->whereHas('role', fn($q) => $q->where('status', 'mahasiswa'))
                ->having('penginstalans_count', '>', 0)
                ->orderByDesc('penginstalans_count')
                ->orderBy('first_penginstalan_at') // 🔑 tie breaker
                ->limit($limit)
                ->get();


            $labels = $top->pluck('nama')->map(
                fn($n) =>
                strlen($n) > 20 ? substr($n, 0, 17) . '...' : $n
            )->values()->toArray();

            $data = $top->pluck('penginstalans_count')->values()->toArray();
        }

        return view('admin.rank.index', [
            'menu'     => 'rank',
            'type'     => $type,
            'semester' => $semester,
            'top'      => $top,
            'labels'   => $labels,
            'data'     => $data,
        ]);
    }
}
