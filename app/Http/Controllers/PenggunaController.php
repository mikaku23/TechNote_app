<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\role;
use App\Models\User;
use App\Models\login_log;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Auth;


class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $roleId  = $request->input('role');
        $tanggal = $request->input('tanggal');

        $query = User::with('role');

        // ================= FILTER SEARCH =================
        // nama / username / qr_code
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('qr_code', 'like', "%{$search}%");
            });
        }

        // Filter role
        if (!empty($roleId)) {
            $query->where('role_id', $roleId);
        }

        // Filter berdasarkan tanggal created_at (single date)
        if (!empty($tanggal)) {
            // pastikan $tanggal bentuk YYYY-MM-DD, input type="date" menghasilkan format ini
            $query->whereDate('created_at', '=', $tanggal);
        }

        // pake withQueryString() biar query params (search, role, tanggal) tetap saat paginate link diklik
        $datauser = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $roles = Role::all();

        return view('admin.pengguna.index', [
            'datauser' => $datauser,
            'roles' => $roles,
            'menu' => 'pengguna',
            'title' => 'Data Pengguna',

        ]);
    }



    public function create()
    {
        $questions = config('secure.security_questions');
        $randomQuestion = $questions[array_rand($questions)];
        // Ambil semua role
        $roles = role::all();

        // Cek apakah sudah ada user dengan role admin
        $adminRole = role::where('status', 'admin')->first();
        $adminSudahAda = false;

        if ($adminRole) {
            $adminSudahAda = User::where('role_id', $adminRole->id)->exists();
        }

        // Jika admin sudah ada, hilangkan role admin dari daftar pilihan
        if ($adminSudahAda) {
            $roles = $roles->reject(function ($role) {
                return $role->status === 'admin';
            });
        }

        return view('admin.pengguna.create', [
            'menu' => 'pengguna',
            'title' => 'Tambah User',
            'roles' => $roles,
            'randomQuestion' => $randomQuestion,
        ]);
    }

    public function store(Request $request, WhatsappService $waService)
    {
        $nm = $request->foto;
        $namaFile = $nm->getClientOriginalName();

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'nullable|string|unique:users,nim',
            'nip' => 'nullable|string|unique:users,nip',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
             'no_hp' => 'required|string|unique:users,no_hp|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'security_question' => 'nullable|string|max:255',
            'security_answer' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
        ],[
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.unique' => 'Nomor HP sudah digunakan.',  
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
        ]
    );

        // ================= SIMPAN USER =================
        $user = new User();
        $user->nama = $validated['nama'];
        $user->nim = $validated['nim'] ?? null;
        $user->nip = $validated['nip'] ?? null;
        $user->username = $validated['username'];
        $user->password = bcrypt($validated['password']);
        // normalisasi no hp
        $noHp = preg_replace('/[^0-9]/', '', $validated['no_hp']); // hapus karakter non-angka
        if (substr($noHp, 0, 1) === '0') {
            $noHp = '62' . substr($noHp, 1); // ganti 0 awal jadi 62
        }
        $user->no_hp = $noHp;
        $user->security_question = $validated['security_question'];
        $user->security_answer = bcrypt($validated['security_answer']);
        $user->foto = $namaFile;
        $user->role_id = $validated['role_id'];

        $nm->move(public_path('foto'), $namaFile);
        $user->save();


        // ================= GENERATE QR =================
        $roleCode = match ($user->role_id) {
            1 => 'ADM',
            2 => 'DSN',
            3 => 'MHS',
            default => 'USR',
        };

        // token acak unik (misal 10 karakter)
        $token = strtoupper(Str::random(10));

        // format QR
        $qrCode = "USER-{$token}-{$roleCode}";

        // URL QR (background putih otomatis dari API)
        $qrUrl = 'https://bwipjs-api.metafloor.com/?bcid=qrcode'
            . '&text=' . urlencode($qrCode)
            . '&scale=6';

        $user->update([
            'qr_code' => $qrCode,
            'qr_url'  => $qrUrl,
        ]);


        // ================= KIRIM WHATSAPP =================
        if ($user->no_hp) {

            $tanggal = Carbon::now('Asia/Jakarta')->format('d F Y');

           $msg = "Halo {$user->nama}, akun anda berhasil dibuat melalui pihak Teknisi.\n\n"
                . "Berikut data akun anda:\n"
                . "Username: {$user->username}\n"
                . "Role: {$roleCode}\n"
                . "QR Code akun anda (klik link berikut):\n"
                . "{$user->qr_url}\n"
                . "*Harap tidak membagikan tautan ini kepada siapa pun karena bersifat pribadi.*\n\n"
                . "QR Code ini berfungsi sebagai identitas digital.\n"
                . "Jika lupa password, pemulihan akun dapat dilakukan "
                . "dengan datang ke Ruang Teknisi dan menunjukkan QR Code.\n"
                . "Pemulihan mandiri juga tersedia di website TechNoteAPP.\n\n"
                . "_{$tanggal}_\n"
                . "_Sent via TechNoteAPP (powered by Green.com)_";

            $waService->sendMessage($user->no_hp, $msg);
        }

        // ================= LOG AKTIVITAS =================
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
                    'activity'     => 'Menambahkan pengguna baru',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengguna.index');
    }



    public function createMahasiswa()
    {
        $questions = config('secure.security_questions');
        $randomQuestion = $questions[array_rand($questions)];
        return view('admin.pengguna.create_mahasiswa', [
            'menu' => 'pengguna',
            'title' => 'Tambah Mahasiswa',
            'randomQuestion' => $randomQuestion,
        ]);
    }

    public function storeMahasiswa(Request $request, WhatsappService $waService)
    {
        $nm = $request->foto;
        $namaFile = $nm->getClientOriginalName();

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'required|string|unique:users,nim',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
             'no_hp' => 'required|string|unique:users,no_hp|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah digunakan.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.unique' => 'Nomor HP sudah digunakan.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $user = new User();
        $user->nama = $validated['nama'];
        $user->nim = $validated['nim'];
        $user->username = $validated['username'];
        $user->password = bcrypt($validated['password']);
        $user->security_question = $validated['security_question'];
        $user->security_answer = bcrypt($validated['security_answer']);
        // normalisasi no hp
        $noHp = preg_replace('/[^0-9]/', '', $validated['no_hp']); // hapus karakter non-angka
        if (substr($noHp, 0, 1) === '0') {
            $noHp = '62' . substr($noHp, 1); // ganti 0 awal jadi 62
        }
        $user->no_hp = $noHp;
        $user->foto = $namaFile;
        $user->role_id = 3; // mahasiswa

        $nm->move(public_path('/foto'), $namaFile);
        $user->save();

        // === GENERATE QR CODE MAHASISWA ===
        $roleCode = 'MHS';

        // token acak unik (misal 10 karakter)
        $token = strtoupper(Str::random(10));

        // format QR
        $qrCode = "USER-{$token}-{$roleCode}";

        // URL QR (background putih otomatis dari API)
        $qrUrl = 'https://bwipjs-api.metafloor.com/?bcid=qrcode'
            . '&text=' . urlencode($qrCode)
            . '&scale=6';

        $user->update([
            'qr_code' => $qrCode,
            'qr_url'  => $qrUrl,
        ]);

        // === KIRIM NOTIFIKASI WHATSAPP SETELAH QR DIBUAT ===
        if ($user->no_hp) {
            $tanggal = now('Asia/Jakarta')->format('d F Y');

           $msg = "Halo {$user->nama}, akun anda berhasil dibuat melalui pihak Teknisi.\n\n"
                . "Berikut data akun anda:\n"
                . "Username: {$user->username}\n"
                . "Role: {$roleCode}\n"
                . "QR Code akun anda (klik link berikut):\n"
                . "{$user->qr_url}\n"
                . "*Harap tidak membagikan tautan ini kepada siapa pun karena bersifat pribadi.*\n\n"
                . "QR Code ini berfungsi sebagai identitas digital.\n"
                . "Jika lupa password, pemulihan akun dapat dilakukan "
                . "dengan datang ke Ruang Teknisi dan menunjukkan QR Code.\n"
                . "Pemulihan mandiri juga tersedia di website TechNoteAPP.\n\n"
                . "_{$tanggal}_\n"
                . "_Sent via TechNoteAPP (powered by Green.com)_";

            $waService->sendMessage($user->no_hp, $msg);
        }

        // === LOG AKTIVITAS ===
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
                    'activity'     => 'Menambahkan data mahasiswa baru',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengguna.index');
    }



    public function createDosen()
    {
        $questions = config('secure.security_questions');
        $randomQuestion = $questions[array_rand($questions)];
        return view('admin.pengguna.create_dosen', [
            'menu' => 'pengguna',
            'title' => 'Tambah Dosen',
            'randomQuestion' => $randomQuestion,
        ]);
    }

    public function storeDosen(Request $request, WhatsappService $waService)
    {
        $nm = $request->foto;
        $namaFile = $nm->getClientOriginalName();

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|string|unique:users,nip',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
             'no_hp' => 'required|string|unique:users,no_hp|max:15',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah digunakan.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.unique' => 'Nomor HP sudah digunakan.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // simpan user
        $user = new User();
        $user->nama = $validated['nama'];
        $user->nip = $validated['nip'];
        $user->username = $validated['username'];
        $user->password = bcrypt($validated['password']);
        $user->security_question = $validated['security_question'];
        $user->security_answer = bcrypt($validated['security_answer']);
        // normalisasi no hp
        $noHp = preg_replace('/[^0-9]/', '', $validated['no_hp']); // hapus karakter non-angka
        if (substr($noHp, 0, 1) === '0') {
            $noHp = '62' . substr($noHp, 1); // ganti 0 awal jadi 62
        }
        $user->no_hp = $noHp;
        $user->foto = $namaFile;
        $user->role_id = 2; // dosen

        $nm->move(public_path('/foto'), $namaFile);
        $user->save();

        // === GENERATE QR CODE DOSEN ===
        $roleCode = 'DSN';

        // token acak unik (misal 10 karakter)
        $token = strtoupper(Str::random(10));

        // format QR
        $qrCode = "USER-{$token}-{$roleCode}";

        // URL QR (background putih otomatis dari API)
        $qrUrl = 'https://bwipjs-api.metafloor.com/?bcid=qrcode'
            . '&text=' . urlencode($qrCode)
            . '&scale=6';

        $user->update([
            'qr_code' => $qrCode,
            'qr_url'  => $qrUrl,
        ]);

        // === KIRIM NOTIFIKASI WHATSAPP ===
        if ($user->no_hp) {
            $tanggal = now('Asia/Jakarta')->format('d F Y');

            $msg = "Halo {$user->nama}, akun anda berhasil dibuat melalui pihak Teknisi.\n\n"
                . "Berikut data akun anda:\n"
                . "Username: {$user->username}\n"
                . "Role: {$roleCode}\n"
                . "QR Code akun anda (klik link berikut):\n"
                . "{$user->qr_url}\n"
                . "*Harap tidak membagikan tautan ini kepada siapa pun karena bersifat pribadi.*\n\n"
                . "QR Code ini berfungsi sebagai identitas digital.\n"
                . "Jika lupa password, pemulihan akun dapat dilakukan "
                . "dengan datang ke Ruang Teknisi dan menunjukkan QR Code.\n"
                . "Pemulihan mandiri juga tersedia di website TechNoteAPP.\n\n"
                . "_{$tanggal}_\n"
                . "_Sent via TechNoteAPP (powered by Green.com)_";


            $waService->sendMessage($user->no_hp, $msg);
        }

        // === LOG AKTIVITAS ===
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
                    'activity'     => 'Menambahkan data dosen baru',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengguna.index');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = role::all();

        return view('admin.pengguna.edit', [
            'menu' => 'pengguna',
            'title' => 'Edit Pengguna',
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'nama' => 'nullable',
            'nim' => 'nullable',
            'nip' => 'nullable',
            'username' => 'nullable',
            'password' => 'nullable',
            'security_question' => 'nullable',
            'security_answer' => 'nullable',
            'role_id' => 'nullable|exists:roles,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'role_id.exists' => 'Role tidak valid.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Update data di database
        $user->nama = $validated['nama'];
        $user->nim = $validated['nim'] ?? null;
        $user->nip = $validated['nip'] ?? null;
        $user->username = $validated['username'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->security_question = $validated['security_question'];
        if (!empty($validated['security_answer'])) {
            $user->security_answer = bcrypt($validated['security_answer']);
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $nm = $request->foto;
            $namaFile = $nm->getClientOriginalName();
            $nm->move(public_path() . '/foto', $namaFile);
            $user->foto = $namaFile;
        }

        $user->role_id = $validated['role_id'];
        $user->save();

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
                    'activity'     => 'Mengedit data pengguna dengan ID dan name:' . $id . ', ' . $user->nama,
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }


        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->forceDelete(); // hapus permanen

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
                    'activity'     => 'Menghapus pengguna dengan ID:' . $id,
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }


        return redirect()
            ->route('pengguna.index');
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.pengguna.show', [
            'user' => $user
        ]);
    }

    public function hapusSemua()
    {
        try {
            $protectedUsernames = [
                'haliqadmin',
                'haliqmhs',
                'haliqdosen',
            ];

            User::withTrashed()
                ->whereNotIn('username', $protectedUsernames)
                ->forceDelete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
