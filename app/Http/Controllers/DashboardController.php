<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\rekap;
use App\Models\software;
use App\Models\perbaikan;
use App\Models\penginstalan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboardAdmin(Request $request)
    {
        // 🔹 Summary progress bar
        $totalUser = User::count();
        $targetUser = 50;
        $persentaseUser = min(100, round(($totalUser / $targetUser) * 100));

        $totalSoftware = Software::count();
        $targetSoftware = 50;
        $persentaseSoftware = min(100, round(($totalSoftware / $targetSoftware) * 100));

        $totalrekap = rekap::count();
        $targetrekap = 50;
        $persentaserekap = min(100, round(($totalrekap / $targetrekap) * 100));

        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        /* =================== 1️⃣ DATA MINGGU INI =================== */
        $labelHari = [];
        $dataInstalasiHari = [];
        $dataPerbaikanHari = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $labelHari[] = $date->format('d M');

            $dataInstalasiHari[] = Penginstalan::whereDate('created_at', $date)->count();
            $dataPerbaikanHari[] = Perbaikan::whereDate('created_at', $date)->count();
        }

        /* =================== 2️⃣ DATA BULAN INI =================== */
        $labelTanggal = [];
        $dataInstalasiTanggal = [];
        $dataPerbaikanTanggal = [];

        $daysInMonth = $now->daysInMonth;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::create($year, $month, $i)->format('Y-m-d');
            $labelTanggal[] = $i;

            $dataInstalasiTanggal[] = Penginstalan::whereDate('created_at', $date)->count();
            $dataPerbaikanTanggal[] = Perbaikan::whereDate('created_at', $date)->count();
        }

        /* =================== 3️⃣ DATA TAHUN INI =================== */
        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $dataInstalasiBulan = [];
        $dataPerbaikanBulan = [];

        for ($i = 1; $i <= 12; $i++) {
            $dataInstalasiBulan[] = Penginstalan::whereYear('created_at', $year)->whereMonth('created_at', $i)->count();
            $dataPerbaikanBulan[] = Perbaikan::whereYear('created_at', $year)->whereMonth('created_at', $i)->count();
        }

        /* =================== 4️⃣ DATA SEMUA TAHUN (DYNAMIC) =================== */
        $labelTahun = Penginstalan::selectRaw('YEAR(created_at) as year')
            ->union(Perbaikan::selectRaw('YEAR(created_at)'))
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year')
            ->toArray();

        $dataSemuaTahun = [];
        foreach ($labelTahun as $yr) {
            for ($i = 1; $i <= 12; $i++) {
                $dataSemuaTahun[$yr]['instalasi'][] = Penginstalan::whereYear('created_at', $yr)->whereMonth('created_at', $i)->count();
                $dataSemuaTahun[$yr]['perbaikan'][] = Perbaikan::whereYear('created_at', $yr)->whereMonth('created_at', $i)->count();
            }
        }

        // Pilih tahun untuk filter dropdown (default: tahun sekarang)
        $thisYear = $request->input('year', $year);

        return view('admin.index', [
            'menu' => 'dashboard',
            'title' => 'Dashboard Admin',

            'totalUser' => $totalUser,
            'persentaseUser' => $persentaseUser,

            'totalSoftware' => $totalSoftware,
            'persentaseSoftware' => $persentaseSoftware,

            'totalrekap' => $totalrekap,
            'persentaserekap' => $persentaserekap,

            // data minggu
            'labelHari' => $labelHari,
            'dataInstalasiHari' => $dataInstalasiHari,
            'dataPerbaikanHari' => $dataPerbaikanHari,

            // data bulan
            'labelTanggal' => $labelTanggal,
            'dataInstalasiTanggal' => $dataInstalasiTanggal,
            'dataPerbaikanTanggal' => $dataPerbaikanTanggal,

            // data tahun ini (12 bulan)
            'labelBulan' => $labelBulan,
            'dataInstalasiBulan' => $dataInstalasiBulan,
            'dataPerbaikanBulan' => $dataPerbaikanBulan,

            // data semua tahun
            'labelTahun' => $labelTahun,
            'dataSemuaTahun' => $dataSemuaTahun,
            'dataInstalasiTahun' => $dataSemuaTahun[$thisYear]['instalasi'] ?? [],
            'dataPerbaikanTahun' => $dataSemuaTahun[$thisYear]['perbaikan'] ?? [],
            'tahunTersedia' => $labelTahun,
            'selectedYear' => $thisYear,
        ]);
    }
}
