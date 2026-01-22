<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\perbaikan;
use Illuminate\Support\Str;
use App\Models\penginstalan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoFinishService
{
    protected $waService;

    public function __construct(\App\Services\WhatsappService $waService)
    {
        $this->waService = $waService;
    }

    public function handleAll(): void
    {
        $this->processPenginstalans();
        $this->processPerbaikans();
    }

    protected function processPenginstalans(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        // Ambil penginstalan yang masih pending dan ada estimasi
        $items = penginstalan::where('status', 'pending')
            ->whereNotNull('estimasi')
            ->get();

        foreach ($items as $item) {
            try {
                DB::transaction(function () use ($item, $now) {
                    // Refresh instance and lock
                    $fresh = penginstalan::where('id', $item->id)->lockForUpdate()->first();
                    if (! $fresh || $fresh->status !== 'pending') return;

                    $mulai = $fresh->created_at instanceof Carbon
                        ? $fresh->created_at->copy()
                        : Carbon::parse($fresh->created_at)->setTimezone('Asia/Jakarta');

                    $estimasiDetik = $this->estimasiToSeconds($fresh->estimasi);
                    $target = $mulai->copy()->addSeconds($estimasiDetik);

                    if ($now->lessThan($target)) return; // belum selesai

                    // buat QR jika belum ada
                    $qrCode = $fresh->qr_code;
                    $qrUrl = $fresh->qr_url;
                    if (empty($qrCode)) {
                        $qrCode = $this->generateUniqueQr('INST', $fresh->id);
                        $qrUrl = 'https://bwipjs-api.metafloor.com/?bcid=qrcode&text=' . urlencode($qrCode) . '&scale=6';
                    }

                    // update DB
                    $fresh->update([
                        'status' => 'berhasil',
                        'qr_code' => $qrCode,
                        'qr_url' => $qrUrl,
                        'updated_at' => Carbon::now('Asia/Jakarta'),
                    ]);

                    // kirim WA jika belum terkirim dan nomor tersedia
                    if (! $fresh->notif_terkirim && $fresh->user?->no_hp) {
                        $this->sendPenginstalanNotification($fresh, $mulai, $target, 'penginstalan');
                        $fresh->update(['notif_terkirim' => true]);
                    }

                    // optional: buat entry user activity jika diperlukan
                    // jika ingin menambah, pastikan kolom login_log_id boleh null atau logic disesuaikan
                });
            } catch (\Throwable $e) {
                // bisa log error, tapi jangan crash scheduler
                Log::error('AutoFinishService penginstalan error: ' . $e->getMessage());
            }
        }
    }

    protected function processPerbaikans(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $items = perbaikan::where('status', 'sedang diperbaiki')
            ->whereNotNull('estimasi')
            ->get();

        foreach ($items as $item) {
            try {
                DB::transaction(function () use ($item, $now) {
                    $fresh = Perbaikan::where('id', $item->id)->lockForUpdate()->first();
                    if (! $fresh || strtolower($fresh->status) !== 'sedang diperbaiki') return;

                    $mulai = $fresh->created_at instanceof Carbon
                        ? $fresh->created_at->copy()
                        : Carbon::parse($fresh->created_at)->setTimezone('Asia/Jakarta');

                    $estimasiDetik = $this->estimasiToSeconds($fresh->estimasi);
                    $target = $mulai->copy()->addSeconds($estimasiDetik);

                    if ($now->lessThan($target)) return;

                    $qrCode = $fresh->qr_code;
                    $qrUrl = $fresh->qr_url;
                    if (empty($qrCode)) {
                        $qrCode = $this->generateUniqueQr('REPAIR', $fresh->id);
                        $qrUrl = 'https://bwipjs-api.metafloor.com/?bcid=qrcode&text=' . urlencode($qrCode) . '&scale=6';
                    }

                    $fresh->update([
                        'status' => 'selesai',
                        'qr_code' => $qrCode,
                        'qr_url' => $qrUrl,
                        'updated_at' => Carbon::now('Asia/Jakarta'),
                    ]);

                    if (! $fresh->notif_terkirim && $fresh->user?->no_hp) {
                        $this->sendPerbaikanNotification($fresh, $mulai, $target);
                        $fresh->update(['notif_terkirim' => true]);
                    }
                });
            } catch (\Throwable $e) {
                Log::error('AutoFinishService perbaikan error: ' . $e->getMessage());
            }
        }
    }

    protected function estimasiToSeconds(?string $estimasi): int
    {
        if (empty($estimasi)) return 0;
        $parts = explode(':', $estimasi);
        $hour = isset($parts[0]) ? (int)$parts[0] : 0;
        $minute = isset($parts[1]) ? (int)$parts[1] : 0;
        $second = isset($parts[2]) ? (int)$parts[2] : 0;
        return $hour * 3600 + $minute * 60 + $second;
    }

    protected function generateUniqueQr(string $prefix, int $id): string
    {
        $nomor = str_pad($id, 6, '0', STR_PAD_LEFT);
        $base = "{$prefix}-{$nomor}-SUCCESS";
        // cek collision
        $exists = DB::table('penginstalans')->where('qr_code', $base)->exists()
            || DB::table('perbaikans')->where('qr_code', $base)->exists();

        if (! $exists) return $base;

        // fallback: tambahkan timestamp/random
        return $base . '-' . now('Asia/Jakarta')->format('YmdHis') . '-' . Str::random(4);
    }

    protected function sendPenginstalanNotification($item, $mulai, $target, $type)
    {
        $durasi = $mulai->diffInMinutes($target);
        $jam = floor($durasi / 60);
        $menit = $durasi % 60;
        $durasiText = ($jam > 0 ? $jam . ' jam ' : '') . $menit . ' menit';

        $tanggalSelesai = $item->tgl_instalasi
            ? Carbon::parse($item->tgl_instalasi)->setTimezone('Asia/Jakarta')->format('d F Y')
            : 'tidak ada data';

        $namaUser = $item->user->nama ?? 'pengguna';
        $versiSoftware = $item->software->versi ?? '-';
        $statusSoftware = $item->status;
        $namaSoftware = $item->software->nama ?? '-';

        $msg = "Halo {$namaUser}, penginstalan anda telah selesai dikerjakan\n\n"
            . "Berikut data penginstalan anda:\n\n"
            . "Nama software: {$namaSoftware}\n"
            . "Versi: {$versiSoftware}\n"
            . "Status penginstalan: {$statusSoftware}\n"
            . "Durasi Pengerjaan: {$durasiText}\n\n"
            . "Silakan datang ke ruang teknisi untuk mengambil perangkat.\n\n"
            . "_{$tanggalSelesai}_\n"
            . "_Sent via TechNoteAPP (powered by Green.com)_";

        $this->waService->sendMessage($item->user->no_hp, $msg);
    }

    protected function sendPerbaikanNotification($item, $mulai, $target)
    {
        $durasi = $mulai->diffInMinutes($target);
        $jam = floor($durasi / 60);
        $menit = $durasi % 60;
        $durasiText = ($jam > 0 ? $jam . ' jam ' : '') . $menit . ' menit';

        $tanggalSelesai = $item->tgl_perbaikan
            ? Carbon::parse($item->tgl_perbaikan)->setTimezone('Asia/Jakarta')->format('d F Y')
            : 'tidak ada data';

        $namaUser = $item->user->nama ?? 'pengguna';

        $msg = "Halo {$namaUser}, perbaikan {$item->nama} anda telah selesai.\n\n"
            . "Berikut data perbaikan anda:\n"
            . "Nama barang: {$item->nama}\n"
            . "Kategori: {$item->kategori}\n"
            . "Status: {$item->status}\n"
            . "Durasi pengerjaan: {$durasiText}\n\n"
            . "Silakan datang ke ruang teknisi untuk mengambil barang.\n\n"
            . "_{$tanggalSelesai}_\n"
            . "_Sent via TechNoteAPP (powered by Green.com)_";

        $this->waService->sendMessage($item->user->no_hp, $msg);
    }
}
