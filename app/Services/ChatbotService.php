<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Perbaikan;
use App\Security\RoleGuard;
use App\Models\Penginstalan;
use App\Security\KeywordGuard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ChatbotService
{
    // endpoint OpenRouter (sesuaikan jika berubah)
    private string $openrouterUrl = 'https://openrouter.ai/api/v1/chat/completions';
    private string $openrouterModel = 'deepseek/deepseek-chat';

    /**
     * Entry point: wajib menerima $user (Auth::user()) dan pesan mentah.
     */
    public function handle($user, string $rawMessage): string
    {
        $message = trim(mb_strtolower($rawMessage));
        $warning = '';

        // 0) KEYWORD GUARD (blokir kata sensitif & aksi terlarang)
        if (KeywordGuard::isBlocked($message)) {
            $reply = "maaf, saya tidak memahami atau tidak memiliki informasi terkait permintaan tersebut.";

            // catat history agar konsisten
            $this->pushHistory($user, $rawMessage, $reply);

            return $reply;
        }


        // 1) PROFANITY check (jenis/profanity.php)
        foreach (config('jenis.profanity', []) as $bad) {
            if (str_contains($message, mb_strtolower($bad))) {
                $warning = 'tolong untuk tidak menggunakan kata atau kalimat kasar ya 😄 ';
                // jangan return langsung — beri prefix pada jawaban
                break;
            }
        }

        // 2) cek apakah user minta kesimpulan / last-topic
        if ($this->isAskingSummary($message)) {
            $summary = $this->summarizeConversation($user, $message);
            $this->pushHistory($user, $message, $summary);
            return $warning . $summary;
        }

        // === HANDLE PROFIL AMBIGU ===
        if ($message === 'profil') {
            $reply = "maksud anda profil saya atau profil STMIK Triguna Dharma?";
            $this->pushHistory($user, $rawMessage, $reply);
            return $warning . $reply;
        }
        // routing lanjutan dari jawaban ambigu
        if (in_array($message, ['profil saya', 'profil akun', 'profilku'])) {
            $this->setLastTopic($user, 'profil_user');
            $reply = $this->handleIntent('profil', $user, $message);
            $this->pushHistory($user, $rawMessage, $reply);
            return $warning . $reply;
        }

        if (str_contains($message, 'profil stmik') || str_contains($message, 'tentang kampus')) {
            $this->setLastTopic($user, 'stmik');
            $reply = $this->handleStmik('profil');
            $this->pushHistory($user, $rawMessage, $reply);
            return $warning . $reply;
        }

        // === TAMBAHAN: SELF QUERY dari config/self_query.php ===
        $selfIntent = $this->detectSelfQueryFromConfig($message);
        if ($selfIntent) {
            $this->setLastTopic($user, $selfIntent);
            $reply = $this->handleSelfQuery($selfIntent, $user);
            $this->pushHistory($user, $message, $reply);
            return $warning . $reply;
        }


        // 3) rule-based intent detection dari semua file config/keywords/*
        // 3) rule-based intent detection dari semua file config/keywords/*
        $intent = $this->detectIntentByKeywords($message);

        // setelah detectIntentByKeywords
        if ($intent) {
            if (!$this->isIntentAllowedForUser($user, $intent)) {
                $role = optional($user->role)->status ?? ($user->role ?? 'guest');
                $msgIntent = $intent;
                $reply = "maaf, fitur \"{$msgIntent}\" tidak tersedia untuk role {$role}.";
                $this->pushHistory($user, $message, $reply);
                return $warning . $reply;
            }



        // set last topic (session)
        $this->setLastTopic($user, $intent);

            // jalankan handler khusus intent
            $reply = $this->handleIntent($intent, $user, $message);
            $this->pushHistory($user, $message, $reply);
            return $warning . $reply;
        }


        // 4) jika tidak ketemu via rule -> fallback ke AI untuk klasifikasi / jawaban
        $aiIntent = $this->classifyIntentWithAI($message);

        if ($aiIntent && $this->intentExists($aiIntent)) {
            // cek izin berdasarkan role user
            if (!$this->isIntentAllowedForUser($user, $aiIntent)) {
                $role = optional($user->role)->status ?? ($user->role ?? 'guest');
                return $warning . "maaf, fitur \"{$aiIntent}\" tidak tersedia untuk role {$role}.";
            }

            // AI menyarankan intent yg ada → jalankan handler
            $this->setLastTopic($user, $aiIntent);
            $reply = $this->handleIntent($aiIntent, $user, $message, true);
            $this->pushHistory($user, $message, $reply);
            return $warning . $reply;
        }


        // 5) AI fallback jawaban (gunakan context)
        $reply = $this->generateAnswerWithAI($user, $message);
        $this->pushHistory($user, $message, $reply);
        return $warning . $reply;
    }

    // === TAMBAHAN: handler self query ===
    private function handleSelfQuery(string $intent, $user): string
    {
        switch ($intent) {
            case 'self_name':
                return "nama anda adalah: {$user->nama}";

            case 'self_username':
                return "username anda adalah: {$user->username}";

            case 'self_role':
                $role = optional($user->role)->status ?? ($user->role ?? 'tidak tersedia');
                return "role anda adalah: {$role}";

            case 'self_password':
                return "maaf tidak bisa menampilkan data tersebut";

            case 'self_all':
                $role = optional($user->role)->status ?? ($user->role ?? 'tidak tersedia');
                return "nama: {$user->nama}\nusername: {$user->username}\nrole: {$role}";


            default:
                return 'maaf, perintah belum dipahami.';
        }

    }


    // === TAMBAHAN: deteksi self query via config (lebih tahan banting) ===
    private function detectSelfQueryFromConfig(string $message): ?string
    {
        $m = mb_strtolower($message);

        // 1) Direct quick-check (menangkap variasi umum, prioritas tertinggi)
        $direct = [
            'self_password'  => ['\b(password|kata sandi|sandi|pw)\b'],
            'self_username'  => ['\b(username|user ?name|nama pengguna|user)\b'],
            'self_name'      => ['\b(nama saya|siapa nama saya|siapa saya|siapa namaku|namaku|nama)\b'],
            'self_role'      => ['\b(role saya|status saya|role|peran saya|peran|jabatan)\b'],
            'self_all'       => ['\b(profil saya|data saya|tentang saya|info tentang saya|tentangku)\b'],
        ];

        foreach ($direct as $intent => $patterns) {
            foreach ($patterns as $pat) {
                if (preg_match("/{$pat}/u", $m)) {
                    return $intent;
                }
            }
        }

        // 2) Jika ada config, coba cocokkan menggunakan struktur 'fields' (struktur baru)
        $config = config('self_query');
        if ($config && !empty($config['fields']) && is_array($config['fields'])) {
            foreach (($config['fields'] ?? []) as $field => $group) {
                foreach (($group['trigger'] ?? []) as $t) {
                    $pattern = '/\b' . preg_quote(mb_strtolower($t), '/') . '\b/u';
                    if (preg_match($pattern, $m)) {
                        return 'self_' . $field;
                    }
                }
            }

            foreach (($config['trigger'] ?? []) as $t) {
                $pattern = '/\b' . preg_quote(mb_strtolower($t), '/') . '\b/u';
                if (preg_match($pattern, $m)) {
                    return 'self_all';
                }
            }
        }

        // 3) Kompatibilitas struktur lama (jika config hanya berisi trigger list)
        if ($config && !empty($config['trigger']) && is_array($config['trigger'])) {
            foreach ($config['trigger'] as $t) {
                $tLower = mb_strtolower($t);
                $pattern = '/\b' . preg_quote($tLower, '/') . '\b/u';
                if (preg_match($pattern, $m)) {
                    if (str_contains($tLower, 'nama')) return 'self_name';
                    if (str_contains($tLower, 'username') || str_contains($tLower, 'user')) return 'self_username';
                    if (str_contains($tLower, 'role') || str_contains($tLower, 'peran') || str_contains($tLower, 'jabatan')) return 'self_role';
                    if (str_contains($tLower, 'password') || str_contains($tLower, 'kata sandi') || str_contains($tLower, 'sandi')) return 'self_password';
                    return 'self_all';
                }
            }
        }

        return null;
    }



    /* =========================
       INTENT DETECTION (rule-based)
       ========================= */
    private function detectIntentByKeywords(string $message): ?string
    {
        // ambil semua file under config('keywords')
        $keywords = config('keywords', []);

        foreach ($keywords as $key => $group) {
            // group expected shape: ['trigger'=>[...], 'response'=>...]
            $triggers = $group['trigger'] ?? [];

            foreach ($triggers as $t) {
                // word boundary matching: mencegah false positive
                $pattern = '/\b' . preg_quote(mb_strtolower($t), '/') . '\b/u';
                if (preg_match($pattern, $message)) {
                    return $key; // nama file / intent
                }
            }
        }

        return null;
    }

    private function intentExists(string $intent): bool
    {
        return array_key_exists($intent, config('keywords', []));
    }

    /**
     * Cek apakah sebuah intent boleh diakses oleh user berdasarkan role.
     */
    private function isIntentAllowedForUser($user, string $intent): bool
    {
        $role = optional($user->role)->status ?? ($user->role ?? null);
        if (!$role) return false;

        // normalisasi
        $role = mb_strtolower(trim($role));
        $intent = mb_strtolower(trim($intent));

        return RoleGuard::allowed($role, $intent);
    }



    /* =========================
       HANDLER INTENT (rule responses + dynamic DB)
       ========================= */
    private function handleIntent(string $intent, $user, string $message, bool $fromAi = false): string
    {
        // simple routing by intent name
        switch ($intent) {
            case 'greeting':
                return $this->getRandomResponse('greeting') ?? 'halo, ada yang bisa dibantu?';

            case 'kontak':
            case 'contact':
                // cek apakah user sudah kirim pesan hari ini
                $today = Carbon::today()->toDateString();
                $c = Contact::where('user_id', $user->id)->whereDate('created_at', $today)->latest()->first();
                if ($c) return "menurut data, Anda sudah mengirim pesan hari ini pada " . $c->created_at->format('H:i');
                return $this->getRandomResponse('kontak') ?? 'silakan gunakan form contact untuk mengirim pesan.';

            case 'penginstalan':
                $last = Penginstalan::where('user_id', $user->id)->latest()->first();
                if (!$last) {
                    if ($fromAi) return 'tidak ditemukan catatan penginstalan untuk Anda.';
                    return $this->getRandomResponse('penginstalan') ?? 'untuk penginstalan, cek menu penginstalan.';
                }

                $estimasiMenit = 0;
                if ($last->estimasi) {
                    $t = Carbon::createFromTimeString($last->estimasi);
                    $estimasiMenit = $t->hour * 60 + $t->minute;
                }
                $tanggal = $last->created_at ? $last->created_at->format('d F Y') : 'tidak tersedia';
                $software = $last->software->nama ?? 'tidak tersedia';

                return implode("\n", [
                    "penginstalan terakhir:",
                    "software: {$software}",
                    "status: {$last->status}",
                    "estimasi: {$estimasiMenit} menit",
                    "tanggal: {$tanggal}",
                ]);

            case 'perbaikan':
                $last = Perbaikan::where('user_id', $user->id)->latest()->first();
                if (!$last) {
                    if ($fromAi) return 'tidak ditemukan catatan perbaikan untuk Anda.';
                    return $this->getRandomResponse('perbaikan') ?? 'tidak ada catatan perbaikan.';
                }

                $estimasiMenit = 0;
                if ($last->estimasi) {
                    $t = Carbon::createFromTimeString($last->estimasi);
                    $estimasiMenit = $t->hour * 60 + $t->minute;
                }
                $tanggal = $last->created_at ? $last->created_at->format('d F Y') : 'tidak tersedia';

                return implode("\n", [
                    "perbaikan terakhir:",
                    "nama: {$last->nama}",
                    "status: {$last->status}",
                    "estimasi: {$estimasiMenit} menit",
                    "tanggal: {$tanggal}",
                ]);

            case 'penginstalan_status':
                $last = Penginstalan::where('user_id', $user->id)->latest()->first();
                if ($last) {
                    return "status penginstalan terakhir: {$last->status},\n estimasi: {$last->estimasi}";
                }
                return 'tidak ditemukan catatan penginstalan.';

            case 'perbaikan_status':
                $last = Perbaikan::where('user_id', $user->id)->latest()->first();
                if ($last) {
                    return "status perbaikan terakhir: {$last->status},\n estimasi: {$last->estimasi}";
                }
                return 'tidak ditemukan catatan perbaikan.';

            case 'rekap':
                // normalisasi role
                $role = mb_strtolower(optional($user->role)->status ?? ($user->role ?? ''));

                if ($role === 'mahasiswa') {
                    $rows = Penginstalan::where('user_id', $user->id)->latest()->get();
                    if ($rows->isEmpty()) {
                        return 'tidak ada catatan penginstalan untuk Anda.';
                    }

                    $out = [];

                    foreach ($rows as $r) {
                        $waktu = $r->created_at
                            ? $r->created_at->format('Y-m-d \j\a\m H:i')
                            : 'tidak tersedia';

                        $out[] = implode("\n", [
                            "penginstalan pada:",
                            "tanggal: $waktu",
                            "software: " . ($r->software->nama ?? 'tidak tersedia'),
                            "status: " . ($r->status ?? 'tidak tersedia'),
                            "estimasi: " . ($r->estimasi ?? 'tidak tersedia'),
                        ]);
                    }

                    // pemisah antar record
                    return implode("\n\n", $out);
                }

                if ($role === 'dosen') {
                    $rows = Perbaikan::where('user_id', $user->id)->latest()->get();
                    if ($rows->isEmpty()) {
                        return 'tidak ada catatan perbaikan untuk Anda.';
                    }

                    $out = [];
                    foreach ($rows as $r) {
                        // tampilkan fields yang relevan, tanpa ID
                        $out[] = implode("\n", [
                            "nama: " . ($r->nama ?? 'tidak tersedia'),
                            "status: " . ($r->status ?? 'tidak tersedia'),
                            "estimasi: " . ($r->estimasi ?? 'tidak tersedia'),
                            "tanggal: " . ($r->created_at ? $r->created_at->toDateString() : 'tidak tersedia'),
                        ]);
                    }

                    return implode("\n\n", $out);
                }

                // default: perilaku lama untuk role selain mahasiswa/dosen
                $peng = Penginstalan::where('user_id', $user->id)->count();
                $per  = Perbaikan::where('user_id', $user->id)->count();
                return "rekap: penginstalan {$peng}, perbaikan {$per}.";


            case 'self_query':

            case 'kampus_trigger':
            case 'stmik':
                return $this->handleStmik($message);


            case 'profil':

                // JIKA ADA KATA STMik → JANGAN MASUK PROFIL USER
                if (str_contains(strtolower($message), 'stmik')) {
                    return $this->handleStmik($message);
                }

                $totalPeng = Penginstalan::where('user_id', $user->id)->count();
                $totalPer  = Perbaikan::where('user_id', $user->id)->count();

                if (optional($user->role)->status === 'mahasiswa') {
                    return "nama: {$user->nama}\nusername: {$user->username}\nrole: mahasiswa\npenginstalan: {$totalPeng}";
                }

                if (optional($user->role)->status === 'dosen') {
                    return "nama: {$user->nama}\nusername: {$user->username}\nrole: dosen\nperbaikan: {$totalPer}";
                }

                return "nama: {$user->nama}\nusername: {$user->username}\nrole: " . (optional($user->role)->status ?? 'tidak tersedia') . "\npenginstalan: {$totalPeng}\nperbaikan: {$totalPer}";



                // add more intent handlers as needed...

            default:
                // jika ada response statis di file keyword, kembalikan itu
                $resp = $this->getRandomResponse($intent);
                if ($resp) return $resp;

                return 'maaf, perintah belum dipahami.';
        }
    }

    /* =========================
       STMIK info (reuse existing logic)
       ========================= */
    private function handleStmik(string $message): string
    {
        $data = config('jenis.datastmik', []);

        $map = [
            'profil'        => ['profil', 'tentang'],
            'visi'          => ['visi'],
            'misi'          => ['misi'],
            'program_studi' => ['prodi', 'program studi', 'jurusan'],
            'akreditasi'    => ['akreditasi'],
            'fasilitas'     => ['fasilitas', 'lab'],
            'kontak'        => ['kontak', 'alamat', 'telepon'],
            'lokasi'        => ['lokasi', 'alamat kampus'],
        ];

        foreach ($map as $key => $keywords) {
            foreach ($keywords as $k) {
                if (preg_match('/\b' . preg_quote($k, '/') . '\b/u', $message)) {
                    return $this->format($data[$key] ?? null);
                }
            }
        }

        return 'Informasi STMIK: tersedia topik profil, visi, misi, program studi, akreditasi, fasilitas, kontak.';
    }

    private function format($data): string
    {
        if (!$data) return 'data belum tersedia.';
        if (is_string($data)) return $data;

        $out = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $item) $out .= "- {$item}\n";
            } else $out .= ucfirst($k) . ": {$v}\n";
        }
        return trim($out);
    }

    private function getRandomResponse(string $intent): ?string
    {
        $keywords = config('keywords', []);
        $group = $keywords[$intent] ?? null;
        if (!$group) return null;

        $responses = $group['response'] ?? [];
        if (empty($responses)) return null;

        return is_array($responses) ? $responses[array_rand($responses)] : $responses;
    }

    /* =========================
       SESSION: last_topic & history
       ========================= */
    private function setLastTopic($user, string $topic): void
    {
        Session::put('last_topic_' . $user->id, $topic);
    }

    private function pushHistory($user, string $in, string $out): void
    {
        $key = 'chat_history_' . $user->id;
        $history = Session::get($key, []);
        $history[] = ['in' => $in, 'out' => $out, 'time' => now()->toDateTimeString()];
        // batasi panjang history agar tidak berlebihan
        if (count($history) > 20) $history = array_slice($history, -20);
        Session::put($key, $history);
    }

    /* =========================
       SUMMARY (AI-assisted)
       ========================= */
    private function isAskingSummary(string $message): bool
    {
        return preg_match('/\b(kesimpulan|apa yang kita bahas|ringkasan|simpulkan)\b/u', $message);
    }

    private function summarizeConversation($user, string $promptSuffix = ''): string
    {
        $key = 'chat_history_' . $user->id;
        $history = Session::get($key, []);
        if (empty($history)) return 'Belum ada percakapan sebelumnya untuk disimpulkan.';

        // gabungkan beberapa pasangan input/output
        $text = "";
        foreach ($history as $h) {
            $text .= "User: " . $h['in'] . "\nBot: " . $h['out'] . "\n";
        }

        $system = "Anda adalah asisten ringkas untuk sistem helpdesk STMIK. Buatkan ringkasan singkat (3-4 kalimat) dari percakapan berikut dan berikan rekomendasi tindakan jika perlu.";
        $userPrompt = "Percakapan:\n" . $text . "\nInstruksi: Buat ringkasan singkat dan poin tindakan.";

        $aiResp = $this->callOpenRouter([$system, $userPrompt]);

        return $aiResp ?? 'Maaf, gagal membuat ringkasan saat ini.';
    }

    /* =========================
       AI: klasifikasi intent
       ========================= */
    private function classifyIntentWithAI(string $message): ?string
    {
        // buat prompt yang menanyakan intent dari daftar intents yang tersedia
        $available = array_keys(config('keywords', []));
        $system = "Anda adalah classifier. Klasifikasikan user message menjadi salah satu intent berikut (pilih satu) dalam format: INTENT_NAME. Intents: " . implode(', ', $available) . ". Kalau tidak relevan, jawab: none.";
        $userPrompt = "Message: \"{$message}\"";

        $resp = $this->callOpenRouter([$system, $userPrompt]);

        if (!$resp) return null;

        // coba ekstrak intent dari jawaban (ambil kata pertama yang cocok)
        $respLower = mb_strtolower($resp);
        foreach ($available as $a) {
            if (str_contains($respLower, mb_strtolower($a))) return $a;
        }
        // jika AI menjawab none atau tidak ada match
        return null;
    }

    /* =========================
       AI: generate answer (fallback)
       ========================= */
    private function generateAnswerWithAI($user, string $message): string
    {
        $historyKey = 'chat_history_' . $user->id;
        $history = Session::get($historyKey, []);
        $recent = array_slice($history, -5);

        $context = "";
        foreach ($recent as $h) {
            $context .= "User: {$h['in']}\nBot: {$h['out']}\n";
        }

        $system = "Anda adalah asisten teknis singkat untuk STMIK Triguna Dharma. Gunakan data yang relevan bila ada. Jawaban harus singkat (maks 3 kalimat). Jika butuh data spesifik (status, tanggal), minta user memberikan info tersebut.";
        $userPrompt = "Context:\n{$context}\nUser sekarang: {$message}\nTolong jawab sesuai konteks, atau minta info yang kurang.";

        $resp = $this->callOpenRouter([$system, $userPrompt]);

        return $resp ?? 'Maaf, saya belum dapat menjawab. Coba tanyakan dengan kalimat yang lebih spesifik.';
    }

    /* =========================
       LOW-LEVEL: call OpenRouter
       - gunakan env('OPENROUTER_API_KEY')
       - wajib header: Authorization, HTTP-Referer (app url), X-Title
       ========================= */
    private function callOpenRouter(array $parts): ?string
    {
        try {
            $apiKey = env('OPENROUTER_API_KEY', null);
            if (!$apiKey) return null;

            // build message array
            $messages = [];
            // first element is system if provided
            if (!empty($parts[0])) $messages[] = ['role' => 'system', 'content' => $parts[0]];
            // next is user prompt
            $messages[] = ['role' => 'user', 'content' => ($parts[1] ?? $parts[0])];

            $resp = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => config('app.url', 'http://localhost'),
                'X-Title' => config('app.name', 'TechNoteApp'),
            ])->timeout(15)->post($this->openrouterUrl, [
                'model' => $this->openrouterModel,
                'messages' => $messages,
                'temperature' => 0.2,
                'max_tokens' => 512,
            ]);

            if (!$resp->ok()) {
                // debug minimal, jangan bocorkan ke user
                return null;
            }

            $json = $resp->json();

            // sesuai spec: choices[0].message.content
            $content = $json['choices'][0]['message']['content'] ?? null;
            if (is_array($content)) $content = implode("\n", $content);
            return $content ? trim($content) : null;
        } catch (\Throwable $e) {
            // silent fail: kembalikan null supaya fallback rule-based dipakai
            return null;
        }
    }
}
