<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\rekap;
use App\Models\software;
use App\Models\login_log;
use App\Models\perbaikan;
use App\Models\penginstalan;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboardAdmin(Request $request)
    {
        // hitung user yang sedang online
        $totalUser = login_log::where('status', 'online')->distinct('user_id')->count('user_id');

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

    private function generateSuccessQr()
    {
        $last = Penginstalan::whereNotNull('qr_code')
            ->where('qr_code', 'like', 'INST-%-SUCCESS')
            ->orderBy('id', 'desc')
            ->first();

        if ($last && preg_match('/INST-(\d+)-SUCCESS/', $last->qr_code, $m)) {
            $next = (int)$m[1] + 1;
        } else {
            $next = 1;
        }

        return 'INST-' . str_pad($next, 6, '0', STR_PAD_LEFT) . '-SUCCESS';
    }


    public function dashboardMahasiswa()
    {
        $userId = Auth::id();
        $threeDaysAgo = Carbon::now('Asia/Jakarta')->subDays(3);

        $penginstalans = Penginstalan::with(['software'])
            ->where('user_id', $userId)
            ->where(function ($q) use ($threeDaysAgo) {
                $q->where('status', '!=', 'berhasil')
                    ->orWhere(function ($q2) use ($threeDaysAgo) {
                        $q2->where('status', 'berhasil')
                            ->where('updated_at', '>=', $threeDaysAgo);
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->through(function ($item) {

                if (empty($item->estimasi)) {
                    $item->estimasi_hitung  = 'tidak ada estimasi';
                    $item->estimasi_selesai = null;
                    return $item;
                }

                $mulai = $item->created_at instanceof Carbon
                    ? $item->created_at->copy()
                    : Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta');

                $parts = explode(':', $item->estimasi);
                $hour   = (int)($parts[0] ?? 0);
                $minute = (int)($parts[1] ?? 0);
                $second = (int)($parts[2] ?? 0);

                $estimasiDetik = ($hour * 3600) + ($minute * 60) + $second;

                $target   = $mulai->copy()->addSeconds($estimasiDetik);
                $sekarang = Carbon::now('Asia/Jakarta');

                if ($sekarang->greaterThanOrEqualTo($target)) {
                    // sudah lewat estimasi
                    $item->estimasi_hitung =
                        $item->status === 'berhasil'
                        ? 'penginstalan selesai'
                        : 'menunggu sinkron sistem';
                } else {
                    // masih hitung mundur
                    $diff = $sekarang->diff($target);
                    $item->estimasi_hitung = sprintf(
                        'sisa: %02d jam %02d menit %02d detik',
                        $diff->h + ($diff->d * 24),
                        $diff->i,
                        $diff->s
                    );
                }

                $item->estimasi_selesai = $target->format('d-m-Y H:i:s');

                // QR hanya ditampilkan jika sudah dibuat oleh sistem otomatis
                $item->qr_ready = !empty($item->qr_url);

                return $item;
            });

        return view('mahasiswa.index', [
            'menu'          => 'dashboard',
            'title'         => 'Dashboard Mahasiswa',
            'penginstalans' => $penginstalans
        ]);
    }


    public function dashboardDosen()
    {
        $userId = Auth::id();
        $threeDaysAgo = Carbon::now('Asia/Jakarta')->subDays(3);

        $perbaikans = Perbaikan::with(['user'])
            ->where('user_id', $userId)
            ->where(function ($q) use ($threeDaysAgo) {
                $q->where('status', '!=', 'selesai')
                    ->orWhere(function ($q2) use ($threeDaysAgo) {
                        $q2->where('status', 'selesai')
                            ->where('updated_at', '>=', $threeDaysAgo);
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->through(function ($item) {

                if (empty($item->estimasi)) {
                    $item->estimasi_hitung  = 'tidak ada estimasi';
                    $item->estimasi_selesai = null;
                    return $item;
                }

                $mulai = $item->created_at instanceof Carbon
                    ? $item->created_at->copy()
                    : Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta');

                $parts = explode(':', $item->estimasi);
                $hour   = (int)($parts[0] ?? 0);
                $minute = (int)($parts[1] ?? 0);
                $second = (int)($parts[2] ?? 0);

                $estimasiDetik = ($hour * 3600) + ($minute * 60) + $second;

                $target   = $mulai->copy()->addSeconds($estimasiDetik);
                $sekarang = Carbon::now('Asia/Jakarta');

                if ($sekarang->greaterThanOrEqualTo($target)) {
                    $item->estimasi_hitung =
                        $item->status === 'selesai'
                        ? 'perbaikan selesai'
                        : 'menunggu sinkron sistem';
                } else {
                    $diff = $sekarang->diff($target);
                    $item->estimasi_hitung = sprintf(
                        'sisa: %02d jam %02d menit %02d detik',
                        $diff->h + ($diff->d * 24),
                        $diff->i,
                        $diff->s
                    );
                }

                $item->estimasi_selesai = $target->format('d-m-Y H:i:s');

                $item->qr_ready = !empty($item->qr_url);

                $item->tgl_perbaikan_formatted = $item->tgl_perbaikan
                    ? Carbon::parse($item->tgl_perbaikan)
                    ->setTimezone('Asia/Jakarta')
                    ->format('d-m-Y')
                    : 'tidak ada data';

                return $item;
            });

        return view('dosen.index', [
            'menu'       => 'dashboard',
            'title'      => 'Dashboard Dosen',
            'perbaikans' => $perbaikans
        ]);
    }
}
