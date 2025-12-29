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

    // Bot awareness: profil singkat dan fungsi (ditulis baku sesuai KBBI)
    private array $botProfile = [
        'nama' => 'Chatbot TechNote',
        'deskripsi' => 'Membantu mahasiswa dalam mengakses informasi seputar STMIK Triguna Dharma dan layanan pada website TechNote APP. Bot memberikan informasi tentang penginstalan, perbaikan, rekap, dan kontak layanan.',
        'fungsi' => 'Menjawab pertanyaan terkait penginstalan perangkat lunak, perbaikan perangkat keras, status layanan, serta memberikan panduan ringkas dan rujukan ke operator apabila diperlukan.',
        'batasan' => 'Bot tidak dapat mengakses data sensitif (kata sandi) dan bergantung pada catatan tersimpan di basis data; untuk tindakan administratif yang memerlukan otorisasi, harap hubungi operator.'
    ];


    /**
     * Entry point: wajib menerima $user (Auth::user()) dan pesan mentah.
     */
    public function handle($user, string $rawMessage): string
    {
        $message = trim(mb_strtolower($rawMessage));
        $warning = '';
        $detectedIntents = [];

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

        // --- START: greeting realtime (sisipkan di handle() setelah profanity check) ---
        $greetPrefix = $this->getGreetingPrefix($message);
        if ($this->isGreetingOnly($message)) {
            $reply = $greetPrefix . " Saya Chatbot layanan teknisi STMIK Triguna Dharma. Ada yang bisa dibantu mengenai penginstalan, perbaikan, rekap, atau contact?";
            $this->pushHistory($user, $rawMessage, $reply);
            return $reply;
        }
        // --- END: greeting realtime ---


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

        // jika user nanya tentang bot
        $botQuery = $this->detectBotQueryType($message);
        if ($botQuery) {
            $reply = $this->handleBotQuery($botQuery);
            $this->pushHistory($user, $rawMessage, $reply);
            return $reply;
        }

        // 3) rule-based intent detection dari semua file config/keywords/*

        // === PRE-RESOLVE DATE (GLOBAL MODIFIER) ===
        [$globalStart, $globalEnd] = $this->resolveDateFromMessage($message);
        $matched = $this->detectAllIntents($message);
        if (count($matched) > 1) {
            $responses = [];
            foreach ($matched as $intent) {
                if (!$this->isIntentAllowedForUser($user, $intent)) {
                    $responses[] = "maaf, fitur \"{$intent}\" tidak tersedia untuk role "
                        . (optional($user->role)->status ?? 'guest') . ".";
                    continue;
                }

                $responses[] = $this->handleIntent(
                    $intent,
                    $user,
                    $message,
                    false,
                    $globalStart,
                    $globalEnd
                );
            }

            $final = implode("\n\n", $responses);
            $this->pushHistory($user, $rawMessage, $final);
            return $warning . $final;
        }

        if (count($matched) === 1) {
            $intent = $matched[0];
            if (!$this->isIntentAllowedForUser($user, $intent)) {
                return $warning . "maaf, fitur \"{$intent}\" tidak tersedia untuk role "
                    . (optional($user->role)->status ?? 'guest') . ".";
            }

            $this->setLastTopic($user, $intent);

            $reply = $this->handleIntent(
                $intent,
                $user,
                $message,
                false,
                $globalStart,
                $globalEnd
            );

            $this->pushHistory($user, $rawMessage, $reply);
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

        if ($this->isAskingTime($message)) {
            $reply = $this->handleTimeQuery();
            $this->pushHistory($user, $rawMessage, $reply);
            return $reply;
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
    


    // deteksi apakah user menanyakan tentang bot
    private function detectAboutBot(string $message): bool
    {
        $m = mb_strtolower($message);
        $triggers = [
            'siapa kamu',
            'siapa anda',
            'tentang kamu',
            'tentang anda',
            'tentang bot',
            'mengenai bot',
            'apa tugasmu',
            'apa tugas anda',
            'apa fungsi bot',
            'apa fungsi anda'
        ];
        foreach ($triggers as $t) {
            if (preg_match('/\b' . preg_quote($t, '/') . '\b/u', $m)) return true;
        }
        return false;
    }

    private function detectBotQueryType(string $message): ?string
    {
        $m = mb_strtolower($message);

        if (preg_match('/\b(siapa kamu|siapa anda|kamu siapa|anda siapa)\b/u', $m)) {
            return 'bot_identity';
        }

        if (preg_match('/\b(tentang kamu|tentang anda|mengenai anda|mengenai bot|tentang bot)\b/u', $m)) {
            return 'bot_about';
        }

        if (preg_match('/\b(fungsi kamu|fungsi anda|fungsi bot|fungsi mu)\b/u', $m)) {
            return 'bot_function';
        }

        if (preg_match('/\b(tugas kamu|tugas anda|apa tugasmu|apa tugas anda)\b/u', $m)) {
            return 'bot_task';
        }

        return null;
    }


    // jawab pertanyaan tentang bot (baku, sesuai KBBI)
    private function handleAboutBot($user, string $message): string
    {
        $p = $this->botProfile;
        $now = Carbon::now('Asia/Jakarta');
        $waktu = $now->translatedFormat('l, d F Y H:i'); // butuh setLocale('id') bila ingin output bulan/dlm bahasa id
        $jawab = "{$p['deskripsi']}\n\nFungsi: {$p['fungsi']}\nBatasan: {$p['batasan']}\n\nWaktu server (WIB): {$waktu}";
        return $jawab;
    }

    private function handleBotQuery(string $type): string
    {
        $p = $this->botProfile;

        switch ($type) {

            case 'bot_identity':
                return "Saya adalah {$p['nama']}, sebuah chatbot layanan teknisi.";

            case 'bot_about':
                return "Saya adalah {$p['nama']}. {$p['deskripsi']}";

            case 'bot_function':
                return "Fungsi saya adalah {$p['fungsi']}";

            case 'bot_task':
                return "Tugas saya adalah membantu pengguna memperoleh informasi layanan penginstalan, perbaikan, rekap data, serta memberikan panduan awal secara otomatis.";

            default:
                return "maaf, saya tidak memahami pertanyaan tersebut.";
        }
    }



    /* =========================
       INTENT DETECTION (rule-based)
       ========================= */
    /**
     * Deteksi semua intent dari config keywords/keyword yang muncul di message.
     * Mengembalikan array intent terurut berdasarkan posisi kemunculan (terlebih dahulu muncul => lebih dulu diproses).
     */
    private function detectMainIntent(string $message): ?string
    {
        $keywords = config('keywords', []);
        $message = mb_strtolower($message);

        $candidates = [];

        foreach ($keywords as $intent => $group) {
            // skip modifier
            if (($group['type'] ?? 'intent') !== 'intent') {
                continue;
            }

            foreach ($group['trigger'] ?? [] as $t) {
                if (str_contains($message, mb_strtolower($t))) {
                    $candidates[] = [
                        'intent' => $intent,
                        'priority' => $group['priority'] ?? 0,
                    ];
                    break;
                }
            }
        }

        if (empty($candidates)) {
            return null;
        }

        // ambil intent dengan priority tertinggi
        usort($candidates, fn($a, $b) => $b['priority'] <=> $a['priority']);

        return $candidates[0]['intent'];
    }

    private function detectAllIntents(string $message): array
    {
        $keywords = config('keywords', []);
        $message = mb_strtolower($message);
        $found = [];

        foreach ($keywords as $intent => $group) {
            if (($group['type'] ?? 'intent') !== 'intent') continue;
            foreach ($group['trigger'] ?? [] as $t) {
                if (str_contains($message, mb_strtolower($t))) {
                    $found[$intent] = $group['priority'] ?? 0;
                    break;
                }
            }
        }

        // sort by priority desc, keep intent keys
        arsort($found);
        return array_keys($found);
    }


    private function intentExists(string $intent): bool
    {
        return array_key_exists($intent, config('keywords', []));
    }

    /**
     * Flatten triggers dari config('keywords').
     * Return array: ['intent1' => ['kata1','kata2'], ...]
     */
    private function flattenTriggers(): array
    {
        $res = [];
        $keywords = config('keywords', []);
        foreach ($keywords as $intent => $group) {
            $triggers = $group['trigger'] ?? [];
            // pastikan triggers array dan lowercased
            $res[$intent] = array_map(function ($t) {
                return mb_strtolower(trim($t));
            }, (array)$triggers);
        }
        return $res;
    }

    /**
     * Normalisasi string: lowercase, trim, hapus punctuation extra
     */
    private function normalize(string $s): string
    {
        $s = mb_strtolower($s);
        // ganti non-alphanumeric (kecuali spasi) dengan spasi
        $s = preg_replace('/[^\p{L}\p{N}\s\-\/]/u', ' ', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    /**
     * Detect intent via fuzzy matching ketika rule exact tidak menemukan.
     * Mengembalikan nama intent atau null.
     *
     * Logic:
     *  - flatten semua triggers
     *  - untuk setiap trigger, hitung similar_text (%) dan normalisasi levenshtein distance -> combine score
     *  - pilih skor terbaik; jika >= $minPercent => return intent
     */
    private function detectIntentByTypo(string $message, int $minPercent = 50): ?string
    {
        $msg = $this->normalize($message);
        if ($msg === '') return null;

        $triggers = $this->flattenTriggers();
        $best = ['intent' => null, 'trigger' => null, 'score' => 0];

        foreach ($triggers as $intent => $list) {
            foreach ($list as $trigger) {
                $tr = $this->normalize($trigger);
                if ($tr === '') continue;

                // similar_text percent (0..100)
                similar_text($msg, $tr, $simPercent);

                // levenshtein normalized: compute distance percentage (smaller distance -> higher score)
                // levenshtein requires ascii; use fallback for multibyte by comparing ascii versions
                $m = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $msg) ?: $msg;
                $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $tr) ?: $tr;
                $lev = levenshtein($m, $t);

                $maxLen = max(mb_strlen($m), mb_strlen($t), 1);
                $levPercent = max(0, 100 - ($lev / $maxLen * 100)); // convert to percent-like

                // combine: pilih max(simPercent, levPercent) — lebih toleran
                $score = max($simPercent, $levPercent);

                // prefer exact whole-word matches: jika message contains trigger as word, boost score
                if (preg_match('/\b' . preg_quote($tr, '/') . '\b/u', $msg)) {
                    $score = max($score, 95);
                }

                if ($score > $best['score']) {
                    $best = ['intent' => $intent, 'trigger' => $trigger, 'score' => $score];
                }
            }
        }

        // threshold kecil untuk kata pendek: jika trigger length <=3 but score a bit lower, be careful
        if ($best['score'] >= $minPercent) {
            return $best['intent'];
        }

        return null;
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
    private function handleIntent(string $intent, $user, string $message, bool $fromAi = false, $start = null, $end = null): string

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
                return $this->getRandomResponse('kontak') ?? 'menurut data, pesan anda telah diterima oleh pihak kampus.';

            case 'penginstalan':

                if (mb_strtolower(optional($user->role)->status ?? '') !== 'mahasiswa') {
                    return 'maaf saya tidak mengerti apa yang anda katakan';
                }

                if ($start === '__FUTURE__') {
                    return 'data tersebut belum tersedia.';
                }

                $q = Penginstalan::where('user_id', $user->id);

                if ($start && $end) {
                    $q->whereBetween(
                        'tgl_instalasi',
                        [$start->toDateString(), $end->toDateString()]
                    );
                }

                $total = $q->count();

                if ($total === 0) {
                    return 'tidak ada data penginstalan pada waktu tersebut.';
                }

                $rows = $q
                    ->orderBy('tgl_instalasi', 'desc')
                    ->limit(5)
                    ->get();

                $label = $this->formatPeriodLabel($start, $end);
                $out = [];
                $out[] = "Menampilkan {$rows->count()} dari {$total} penginstalan untuk periode {$label}:";

                foreach ($rows as $r) {
                    $out[] = implode("\n", [
                        "tanggal: " . $r->created_at->format('d F Y H:i'),
                        "software: " . ($r->software->nama ?? 'tidak tersedia'),
                        "status: " . ($r->status ?? 'tidak tersedia'),
                        "estimasi: " . ($r->estimasi ?? 'tidak tersedia'),
                    ]);
                }

                if ($total > 5) {
                    $out[] = "\nData lainnya tidak ditampilkan. Silakan hubungi operator STMIK Triguna Dharma.";
                }

                return implode("\n\n", $out);

            case 'perbaikan':

                if (mb_strtolower(optional($user->role)->status ?? '') !== 'dosen') {
                    return 'maaf saya tidak mengerti apa yang anda katakan';
                }

                if ($start === '__FUTURE__') {
                    return 'data tersebut belum tersedia.';
                }

                $q = Perbaikan::where('user_id', $user->id);

                if ($start && $end) {
                    $q->whereBetween(
                        'tgl_perbaikan',
                        [$start->toDateString(), $end->toDateString()]
                    );
                }


                $total = $q->count();
                if ($total === 0) {
                    return 'tidak ada data perbaikan pada waktu tersebut.';
                }

                // LIMIT 5 DATA TERAKHIR
                $rows = $q
                    ->orderBy('tgl_perbaikan', 'desc')
                    ->limit(5)
                    ->get();

                $label = $this->formatPeriodLabel($start, $end);
                $out = [];
                $out[] = "Menampilkan {$rows->count()} dari {$total} perbaikan untuk periode {$label}:";

                foreach ($rows as $r) {
                    $out[] = implode("\n", [
                        "tanggal: " . $r->created_at->format('d F Y H:i'),
                        "barang: " . ($r->nama ?? 'tidak tersedia'),
                        "kerusakan: " . ($r->kerusakan ?? 'tidak tersedia'),
                        "status: " . ($r->status ?? 'tidak tersedia'),
                        "estimasi: " . ($r->estimasi ?? 'tidak tersedia'),
                    ]);
                }

                if ($total > 5) {
                    $out[] = "\nData lainnya tidak ditampilkan. Silakan hubungi operator STMIK Triguna Dharma.";
                }

                return implode("\n\n", $out);

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
                [$start, $end] = $this->resolveDateFromMessage($message);
                if ($start === '__FUTURE__') {
                    return 'data tersebut belum tersedia.';
                }

                
                $roleNorm = mb_strtolower(optional($user->role)->status ?? '');

                if ($roleNorm === 'mahasiswa') {
                    $q = Penginstalan::where('user_id', $user->id);
                    if ($start && $end) {
                        $q->whereDate('created_at', '>=', $start->toDateString())
                            ->whereDate('created_at', '<=', $end->toDateString());
                    }
                    $rows = $q->latest()->get();
                    if ($rows->isEmpty()) return 'tidak ada catatan penginstalan untuk periode tersebut.';
                    $out = [];
                    foreach ($rows as $r) {
                        $out[] = implode("\n", [
                            "tanggal: " . $r->created_at->format('d F Y H:i'),
                            "software: " . ($r->software->nama ?? 'tidak tersedia'),
                            "status: " . ($r->status ?? 'tidak tersedia'),
                        ]);
                    }
                    return implode("\n\n", $out);
                }

                if ($roleNorm === 'dosen') {
                    $q = Perbaikan::where('user_id', $user->id);
                    if ($start && $end) {
                        $q->whereDate('created_at', '>=', $start->toDateString())
                            ->whereDate('created_at', '<=', $end->toDateString());
                    }
                    $rows = $q->latest()->get();
                    if ($rows->isEmpty()) return 'tidak ada catatan perbaikan untuk periode tersebut.';
                    $out = [];
                    foreach ($rows as $r) {
                        $out[] = implode("\n", [
                            "tanggal: " . $r->created_at->format('d F Y H:i'),
                            "nama: " . ($r->nama ?? 'tidak tersedia'),
                            "status: " . ($r->status ?? 'tidak tersedia'),
                        ]);
                    }
                    return implode("\n\n", $out);
                }

                $peng = Penginstalan::where('user_id', $user->id)->count();
                $per  = Perbaikan::where('user_id', $user->id)->count();
                return "rekap: penginstalan {$peng}, perbaikan {$per}.";

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

    private function getLastTopic($user): ?string
    {
        return Session::get('last_topic_' . $user->id);
    }


    private function ringkasJawaban(string $text, int $maxWords = 30): string
    {
        $words = preg_split('/\s+/', trim($text));

        if (count($words) <= $maxWords) {
            return $text;
        }

        $ringkas = array_slice($words, 0, $maxWords);

        return implode(' ', $ringkas) . '...';
    }


    /* =========================
       AI: generate answer (fallback)
       ========================= */
    /* =========================
   AI: generate answer (fallback)
   ========================= */
    private function generateAnswerWithAI($user, string $message): string
    {
        if (preg_match('/\bstmik\b|\bkampus\b|\btriguna dharma\b/u', $message)) {
            return $this->handleStmik($message);
        }

        $historyKey = 'chat_history_' . $user->id;
        $history = Session::get($historyKey, []);
        $recent = array_slice($history, -5);

        $context = "";
        foreach ($recent as $h) {
            $context .= "User: {$h['in']}\nBot: {$h['out']}\n";
        }

        // role user (admin / mahasiswa / dosen)
        $role = $user->role ?? 'unknown';

        $bot = $this->botProfile;

        $system = "
Anda adalah asisten teknis resmi STMIK Triguna Dharma.

ATURAN WAJIB:
- Role user saat ini: {$role}
- Jangan memberikan data yang tidak diminta secara spesifik.
- Jangan mengarang status, tanggal, atau data.
- Jangan menjawab di luar topik berikut:
  penginstalan, perbaikan, rekap.

PEMBATASAN ROLE:
- mahasiswa → hanya penginstalan & status miliknya
- dosen → hanya perbaikan & status miliknya
- admin → rekap dan informasi umum

JAWABAN:
- Maksimal 3 kalimat
- Jika informasi kurang (tanggal/status), minta user menyebutkan
- Jika pertanyaan di luar topik → jawab persis: tidak tersedia

Profil chatbot: {$bot['deskripsi']}
Fungsi chatbot: {$bot['fungsi']}
";

        $userPrompt = "
Context percakapan sebelumnya:
{$context}

Pertanyaan user:
{$message}

Jawab sesuai aturan di atas.
";

        $resp = $this->callOpenRouter([$system, $userPrompt]);

        // hard guard jika AI mulai melenceng
        if (!$resp) {
            return 'Maaf, saya belum dapat menjawab. Silakan tanyakan lebih spesifik.';
        }

        // filter jawaban terlalu panjang atau beropini
        if (str_word_count($resp) > 30) {
            return $this->ringkasJawaban($resp, 30);
        }


        return trim($resp);
    }


    /* =========================
   WAKTU & GREETING HELPERS
   ========================= */
    private function resolveDateFromMessage(string $message): array
    {
        if ($this->hasFutureTimeKeyword($message)) {
            return ['__FUTURE__', '__FUTURE__'];
        }

       
        $now = Carbon::now('Asia/Jakarta');

        // X hari yang lalu
        if (preg_match('/\b(\d+)\s*hari\s*(yang|yg\s*)?lalu\b/u', $message, $m)) {
            $n = (int) $m[1];
            if ($n >= 1 && $n <= 30) {
                $d = $now->copy()->subDays($n);
                return [$d->startOfDay(), $d->endOfDay()];
            }
        }


        // 1) Cek tanggal spesifik (dd-mm-yyyy, dd mm yyyy, dd-mon-yyyy, dd-mon, dd month year, dll)
        if ($d = $this->parseSpecificDate($message)) {
            return [$d->copy()->startOfDay(), $d->copy()->endOfDay()];
        }

        // 2) Bulan (bulan ini, bulan kemarin, atau bulan tertentu seperti 'des' / 'desember' / '12' / 'dec')
        if ($m = $this->parseMonthYearFromMessage($message)) {
            return [$m['start'], $m['end']];
        }

        // 3) Tahun (tahun ini, kemarin, '2 tahun lalu', atau tahun spesifik)
        if ($y = $this->parseYearFromMessage($message)) {
            return [$y['start'], $y['end']];
        }

        // 4) hari ini / kemarin / besok
        if (str_contains($message, 'hari ini') || str_contains($message, 'sekarang')) {
            return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
        }
        if (str_contains($message, 'kemarin')) {
            $y = $now->copy()->subDay();
            return [$y->startOfDay(), $y->endOfDay()];
        }
        if (str_contains($message, 'besok')) {
            $t = $now->copy()->addDay();
            return [$t->startOfDay(), $t->endOfDay()];
        }

        // default: no constraint (nulls)
        return [null, null];
    }

    private function parseSpecificDate(string $message): ?Carbon
    {
        $m = mb_strtolower($message);

        // formats: dd-mm-yyyy or dd/mm/yyyy or dd.mm.yyyy or dd mm yyyy
        if (preg_match('/\b(\d{1,2})[\/\-\.\s](\d{1,2})[\/\-\.\s](\d{4})\b/u', $m, $p)) {
            try {
                return Carbon::createFromDate((int)$p[3], (int)$p[2], (int)$p[1], 'Asia/Jakarta');
            } catch (\Throwable $e) {
                return null;
            }
        }

        // formats: dd month yyyy  (month could be des/desember/dec/Dec)
        if (preg_match('/\b(\d{1,2})\s+([a-z]+)\s+(\d{4})\b/ui', $m, $p)) {
            $day = (int)$p[1];
            $monthName = mb_strtolower($p[2]);
            $year = (int)$p[3];
            $map = $this->monthNameMap();
            foreach ($map as $key => $num) {
                if (str_contains($monthName, $key)) {
                    try {
                        return Carbon::createFromDate($year, $num, $day, 'Asia/Jakarta');
                    } catch (\Throwable $e) {
                        return null;
                    }
                }
            }
        }

        // formats: dd month  (no year) -> pakai tahun sekarang
        if (preg_match('/\b(\d{1,2})\s+([a-z]+)\b/ui', $m, $p)) {
            $day = (int)$p[1];
            $monthName = mb_strtolower($p[2]);
            $map = $this->monthNameMap();
            foreach ($map as $key => $num) {
                if (str_contains($monthName, $key)) {
                    try {
                        $year = Carbon::now('Asia/Jakarta')->year;
                        return Carbon::createFromDate($year, $num, $day, 'Asia/Jakarta');
                    } catch (\Throwable $e) {
                        return null;
                    }
                }
            }
        }

        // formats: "12 12 2025" (space separated day month year)
        if (preg_match('/\b(\d{1,2})\s+(\d{1,2})\s+(\d{4})\b/u', $m, $p)) {
            try {
                return Carbon::createFromDate((int)$p[3], (int)$p[2], (int)$p[1], 'Asia/Jakarta');
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    private function parseMonthYearFromMessage(string $message): ?array
    {
        $now = Carbon::now('Asia/Jakarta');
        $m = mb_strtolower($message);
        $map = $this->monthNameMap();

        // 'bulan ini' / 'bulan kemarin'
        if (str_contains($m, 'bulan ini')) {
            return ['start' => $now->copy()->startOfMonth(), 'end' => $now->copy()->endOfMonth()];
        }
        if (str_contains($m, 'bulan kemarin') || str_contains($m, 'bulan lalu')) {
            $b = $now->copy()->subMonth();
            return ['start' => $b->startOfMonth(), 'end' => $b->endOfMonth()];
        }

        // month name + optional year e.g. 'desember 2025' or 'des 2025' or '12 2025' or '12/2025'
        if (preg_match('/\b([a-z]+)\s+(\d{4})\b/u', $m, $p)) {
            $mn = $p[1];
            $yr = (int)$p[2];
            foreach ($map as $k => $num) {
                if (str_contains($mn, $k)) {
                    $start = Carbon::createFromDate($yr, $num, 1, 'Asia/Jakarta')->startOfMonth();
                    return ['start' => $start, 'end' => $start->copy()->endOfMonth()];
                }
            }
        }

        // numeric month + optional year: '12 2025' or '12/2025' or '12-2025'
        if (preg_match('/\b(\d{1,2})[\/\-\s](\d{4})\b/u', $m, $p)) {
            $mon = (int)$p[1];
            $yr = (int)$p[2];
            if ($mon >= 1 && $mon <= 12) {
                $start = Carbon::createFromDate($yr, $mon, 1, 'Asia/Jakarta')->startOfMonth();
                return ['start' => $start, 'end' => $start->copy()->endOfMonth()];
            }
        }

        // month name without year -> asumsi tahun sekarang
        foreach ($map as $k => $num) {
            if (str_contains($m, $k) && !preg_match('/\d{4}/', $m)) {
                $year = $now->year;
                $start = Carbon::createFromDate($year, $num, 1, 'Asia/Jakarta')->startOfMonth();
                return ['start' => $start, 'end' => $start->copy()->endOfMonth()];
            }
        }

        return null;
    }

    private function parseYearFromMessage(string $message): ?array
    {
        $now = Carbon::now('Asia/Jakarta');
        $m = mb_strtolower($message);

        // 'tahun ini'
        if (str_contains($m, 'tahun ini')) {
            return ['start' => $now->copy()->startOfYear(), 'end' => $now->copy()->endOfYear()];
        }

        // 'tahun kemarin' or 'tahun lalu'
        if (str_contains($m, 'tahun kemarin') || preg_match('/1\s*tahun\s*lalu/', $m)) {
            $y = $now->copy()->subYear();
            return ['start' => $y->startOfYear(), 'end' => $y->endOfYear()];
        }

        // '2 tahun lalu', '3 tahun lalu' (maks 5)
        if (preg_match('/\b(\d)\s*tahun\s*lalu\b/u', $m, $p)) {
            $n = (int)$p[1];
            if ($n >= 1 && $n <= 5) {
                $y = $now->copy()->subYears($n);
                return ['start' => $y->startOfYear(), 'end' => $y->endOfYear()];
            }
        }

        // specific year '2023'
        if (preg_match('/\b(20\d{2})\b/u', $m, $p)) {
            $yr = (int)$p[1];
            $s = Carbon::createFromDate($yr, 1, 1, 'Asia/Jakarta')->startOfYear();
            return ['start' => $s, 'end' => $s->copy()->endOfYear()];
        }

        return null;
    }

    private function formatPeriodLabel($start, $end): string
    {
        if (!$start || !$end) return 'periode tidak spesifik';
        // jika sama hari -> tampilkan tanggal tunggal
        if ($start->isSameDay($end)) {
            return $start->format('d F Y');
        }
        // jika satu bulan sama
        if ($start->format('Y-m') === $end->format('Y-m')) {
            return $start->format('F Y');
        }
        // kalau tahun sama
        if ($start->format('Y') === $end->format('Y')) {
            return $start->format('Y');
        }
        return $start->format('d F Y') . ' — ' . $end->format('d F Y');
    }


    private function monthNameMap(): array
    {
        return [
            'jan' => 1,
            'januari' => 1,
            'feb' => 2,
            'februari' => 2,
            'mar' => 3,
            'maret' => 3,
            'apr' => 4,
            'april' => 4,
            'may' => 5,
            'mei' => 5,
            'jun' => 6,
            'juni' => 6,
            'jul' => 7,
            'juli' => 7,
            'aug' => 8,
            'agustus' => 8,
            'august' => 8,
            'sep' => 9,
            'september' => 9,
            'oct' => 10,
            'okt' => 10,
            'oktober' => 10,
            'october' => 10,
            'nov' => 11,
            'november' => 11,
            'dec' => 12,
            'des' => 12,
            'desember' => 12,
            'december' => 12,
        ];
    }

    private function hasFutureTimeKeyword(string $message): bool
    {
        $m = mb_strtolower($message);

        $futureKeywords = [
            'bulan depan',
            'tahun depan',
            'minggu depan',
            'hari depan',
            'tanggal depan',
            'besok lusa',
            'lusa',
            'next month',
            'next year',
        ];

        foreach ($futureKeywords as $k) {
            if (str_contains($m, $k)) {
                return true;
            }
        }

        // pola "bulan X depan", "tahun 2026", dll
        if (preg_match('/\b(depan|berikutnya|selanjutnya)\b/u', $m)) {
            return true;
        }

        return false;
    }


    private function getGreetingPrefix(string $message): string
    {
        $glist = config('keywords.greeting.trigger') ?? config('keywords.greeting') ?? config('keyword.greeting') ?? [];
        foreach ($glist as $g) {
            $g = mb_strtolower($g);
            if (str_starts_with($message, $g)) {
                return $this->timeGreeting();
            }
        }
        return '';
    }

    private function isGreetingOnly(string $message): bool
    {
        $glist = config('keywords.greeting.trigger') ?? config('keywords.greeting') ?? config('keyword.greeting') ?? [];
        foreach ($glist as $g) {
            if (trim($message) === mb_strtolower(trim($g))) return true;
        }
        return false;
    }

    private function timeGreeting(): string
    {
        $h = (int) Carbon::now('Asia/Jakarta')->format('H');
        if ($h >= 4 && $h <= 11) return 'Selamat Pagi';
        if ($h >= 11 && $h <= 15) return 'Selamat Siang';
        if ($h >= 15 && $h <= 18) return 'Selamat Sore';
        return 'Selamat Malam';
    }

    // cek apakah user menanyakan waktu/hari (mis: "hari ini hari apa", "sekarang jam berapa")
    private function isAskingTime(string $message): bool
    {
        return preg_match('/\b(hari ini hari apa|hari apa|sekarang jam|jam berapa|tanggal berapa|hari ini)\b/u', mb_strtolower($message));
    }

    // handle query waktu
    private function handleTimeQuery(): string
    {
        $now = Carbon::now('Asia/Jakarta');
        // gunakan format baku: Hari, dd Month YYYY, HH:mm (WIB)
        // jika ingin bahasa Indonesia penuh: Carbon::setLocale('id'); kemudian ->translatedFormat(...)
        $hari = $now->translatedFormat('l'); // butuh locale 'id' untuk 'Senin' dll
        $tanggal = $now->format('d F Y');
        $jam = $now->format('H:i');
        return "Waktu saat ini (WIB): {$hari}, {$tanggal} pukul {$jam}";
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
