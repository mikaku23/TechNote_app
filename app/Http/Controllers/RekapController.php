<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rekap;
use App\Models\login_log;
use App\Models\perbaikan;
use App\Exports\RekapExport;
use App\Models\penginstalan;
use App\Models\UserActivity;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Pagination\LengthAwarePaginator;


class RekapController extends Controller
{
    public function index(Request $request)
    {
        $filterWaktu = $request->input('waktu'); // minggu / bulan / tahun atau angka misal 2023
        $jenis = $request->input('jenis'); // perbaikan / penginstalan / null
        $filterStatus = $request->input('status'); // tersedia / dihapus / null
        $filterTanggal = $request->input('tanggal');

        $datesQuery = DB::table('rekaps')
            ->leftJoin('perbaikans', 'rekaps.perbaikan_id', '=', 'perbaikans.id')
            ->leftJoin('penginstalans', 'rekaps.penginstalan_id', '=', 'penginstalans.id')
            ->selectRaw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan) as tanggal")
            ->whereNotNull(DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"));

        if ($filterTanggal) {
            $datesQuery->whereDate(DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"), $filterTanggal);
        }

        // Filter waktu (minggu ini, bulan ini, tahun ini, atau tahun tertentu)
        if ($filterWaktu) {
            if ($filterWaktu == 'minggu') {
                $datesQuery->whereBetween(
                    DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"),
                    [now()->startOfWeek(), now()->endOfWeek()]
                );
            } elseif ($filterWaktu == 'bulan') {
                $datesQuery->whereMonth(DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"), now()->month)
                    ->whereYear(DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"), now()->year);
            } elseif ($filterWaktu == 'tahun') {
                $datesQuery->whereYear(DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"), now()->year);
            } elseif (is_numeric($filterWaktu)) {
                $datesQuery->whereYear(DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"), $filterWaktu);
            }
        }

        $datesQuery->distinct()->orderBy('tanggal', 'desc');

        $perPage = 10;
        $page = $request->input('page', 1);
        $datesPaginator = $datesQuery->paginate($perPage, ['tanggal'], 'page', $page);

        $items = [];

        foreach ($datesPaginator->items() as $row) {
            $tanggal = $row->tanggal;

            $rekapsForDate = Rekap::withTrashed()
                ->with([
                    'perbaikan' => fn($q) => $q->withTrashed(),
                    'penginstalan' => fn($q) => $q->withTrashed()->with('software')
                ])
                ->where(function ($q) use ($tanggal) {
                    $q->whereHas('perbaikan', fn($q2) => $q2->where('tgl_perbaikan', $tanggal))
                        ->orWhereHas('penginstalan', fn($q2) => $q2->where('tgl_instalasi', $tanggal));
                })
                ->get();

            if ($rekapsForDate->isEmpty()) continue;

            $listPerbaikan = $rekapsForDate->filter(fn($r) => $r->perbaikan)->values();
            $listInstalasi = $rekapsForDate->filter(fn($r) => $r->penginstalan)->values();

            $maxCount = max($listPerbaikan->count(), $listInstalasi->count());

            for ($i = 0; $i < $maxCount; $i++) {
                $perbaikan = $listPerbaikan[$i] ?? null;
                $instalasi = $listInstalasi[$i] ?? null;

                $namaPerbaikan = $perbaikan?->perbaikan?->nama ?? '-';
                $statusPerbaikan = $perbaikan?->perbaikan?->trashed() ? 'dihapus' : ($perbaikan ? 'tersedia' : '-');

                if ($instalasi?->penginstalan) {
                    $namaInstalasi = $instalasi->penginstalan->software->nama ?? '-';
                    $statusInstalasi = $instalasi->penginstalan->trashed() ? 'dihapus' : 'tersedia';
                } else {
                    $namaInstalasi = '-';
                    $statusInstalasi = '-';
                }

                if ($jenis === 'perbaikan' && $namaPerbaikan === '-') continue;
                if ($jenis === 'penginstalan' && $namaInstalasi === '-') continue;
                if ($filterStatus === 'tersedia' && ($statusPerbaikan === 'dihapus' || $statusInstalasi === 'dihapus')) continue;
                if ($filterStatus === 'dihapus' && ($statusPerbaikan === 'tersedia' || $statusInstalasi === 'tersedia')) continue;

                if ($statusPerbaikan !== '-' && $statusInstalasi !== '-' && $statusPerbaikan !== $statusInstalasi) {
                    $statusGabungan = 'gabungan';
                } elseif ($statusPerbaikan !== '-') {
                    $statusGabungan = $statusPerbaikan;
                } elseif ($statusInstalasi !== '-') {
                    $statusGabungan = $statusInstalasi;
                } else {
                    continue;
                }

                $items[] = [
                    'tanggal' => $tanggal,
                    'nama_perbaikan' => $namaPerbaikan,
                    'status_perbaikan' => $statusPerbaikan,
                    'nama_instalasi' => $namaInstalasi,
                    'status_instalasi' => $statusInstalasi,
                    'status' => $statusGabungan,
                ];
            }
        }

        $paginator = new LengthAwarePaginator(
            $items,
            count($items),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $tahunLain = DB::table('rekaps')
            ->leftJoin('perbaikans', 'rekaps.perbaikan_id', '=', 'perbaikans.id')
            ->leftJoin('penginstalans', 'rekaps.penginstalan_id', '=', 'penginstalans.id')
            ->selectRaw("YEAR(COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)) as tahun")
            ->whereNotNull(DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"))
            ->whereYear(DB::raw("COALESCE(penginstalans.tgl_instalasi, perbaikans.tgl_perbaikan)"), '<>', now()->year)
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('admin.rekap.index', [
            'menu' => 'rekap',
            'title' => 'Rekapitulasi Data',
            'rekap' => $paginator,
            'totalData' => count($items),
            'tahunLain' => $tahunLain,
        ]);
    }

    public function exportPdf()
    {
        $perbaikan = Rekap::with(['perbaikan.user'])
            ->whereNotNull('perbaikan_id')
            ->get();

        $penginstalan = Rekap::with(['penginstalan.software', 'penginstalan.user'])
            ->whereNotNull('penginstalan_id')
            ->get();

        $isPdf = true; // penting untuk load gambar via public_path

        $pdf = Pdf::loadView('admin.rekap.export-pdf', [
            'perbaikan' => $perbaikan,
            'penginstalan' => $penginstalan,
            'isPdf' => $isPdf,
        ])->setPaper('a4', 'portrait');

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
                    'activity'     =>  'Mengekspor data rekapitulasi lengkap ke PDF',
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        return $pdf->download('rekap-lengkap.pdf');
    }


    public function exportExcel()
    {
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
                    'activity'     =>  'Mengekspor data rekapitulasi lengkap ke Excel',
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }
        return Excel::download(new \App\Exports\RekapExport, 'rekap-lengkap.xlsx');
    }


    public function print()
    {
        $perbaikan = Rekap::with(['perbaikan.user'])
            ->whereNotNull('perbaikan_id')
            ->get();

        $penginstalan = Rekap::with(['penginstalan.software', 'penginstalan.user'])
            ->whereNotNull('penginstalan_id')
            ->get();

        $isPdf = false; // untuk view print memakai asset()


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
                    'activity'     =>  'Mengeprint data rekapitulasi lengkap',
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        return view('admin.rekap.print', compact('perbaikan', 'penginstalan', 'isPdf'));
    }
}
