<?php

// app/Console/Commands/SendWhatsappNotifications.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\penginstalan;
use App\Models\perbaikan;
use App\Services\WhatsappService;
use Carbon\Carbon;

class SendWhatsappNotifications extends Command
{
    protected $signature = 'notify:wa';
    protected $description = 'Kirim WA otomatis ketika estimasi selesai';

    protected $wa;

    public function __construct(WhatsappService $wa)
    {
        parent::__construct();
        $this->wa = $wa;
    }

    public function handle()
    {
        $now = Carbon::now();

        // Penginstalan
        $instals = penginstalan::where('notif_terkirim', false)
            ->whereNotNull('estimasi')
            ->where('estimasi', '<=', $now)
            ->get();

        foreach ($instals as $i) {
            // ambil nomor telepon dari relasi user (pastikan ada field no_hp)
            $no = $i->user->no_hp ?? null;
            if (!$no) continue;

            $msg = "Penginstalan software {$i->software->nama} diperkirakan sudah selesai pada {$i->estimasi}. Silakan cek / ambil perangkat di ruang teknisi.";
            $sent = $this->wa->sendMessage($this->formatPhone($no), $msg);

            if ($sent) {
                $i->update(['notif_terkirim' => true]);
            }
        }

        // Perbaikan
        $repairs = perbaikan::where('notif_terkirim', false)
            ->whereNotNull('estimasi')
            ->where('estimasi', '<=', $now)
            ->get();

        foreach ($repairs as $r) {
            $no = $r->user->no_hp ?? null;
            if (!$no) continue;

            $msg = "Perbaikan {$r->nama} pada {$r->lokasi} diperkirakan selesai pada {$r->estimasi}. Silakan cek ke ruang teknisi.";
            $sent = $this->wa->sendMessage($this->formatPhone($no), $msg);

            if ($sent) {
                $r->update(['notif_terkirim' => true]);
            }
        }

        return 0;
    }

    protected function formatPhone($no)
    {
        // contoh: ubah 081234567 jadi 6281234567 (Indonesia)
        $n = preg_replace('/[^0-9]/', '', $no);
        if (substr($n, 0, 1) === '0') {
            $n = '62' . substr($n, 1);
        }
        return $n;
    }
}
