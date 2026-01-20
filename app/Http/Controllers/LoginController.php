<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\login_log;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function login()
    {
        return view('login', [
            'title' => 'Login',
        ]);
    }

    public function Auth(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 1. Coba login dengan password baru
        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();
            $user = Auth::user();

            // Arahkan sesuai role
            if ($user->role->status === 'admin') {
                return redirect()->route('dashboard-admin');
            } elseif ($user->role->status === 'dosen') {
                return redirect()->route('dashboard-dosen');
            } elseif ($user->role->status === 'mahasiswa') {
                return redirect()->route('dashboard-mahasiswa');
            } else {
                return redirect()->route('login');
            }
        }

        // 2. Jika gagal login → cek password lama
        $user = User::where('username', $request->username)->first();

        if ($user && $user->old_password) {

            // Jika password lama cocok → beri pesan agar pakai password baru
            if (Hash::check($request->password, $user->old_password)) {
                return back()->withErrors([
                    'password' => 'Password Anda sudah diperbarui. Silakan gunakan password baru.'
                ])->onlyInput('username');
            }
        }
        

        // 3. Jika semua gagal → error default
        return back()->withErrors([
            'username' => 'Username atau password salah',
        ])->onlyInput('username');
    }



    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function myProfile()
    {
        $user = Auth::user(); // ambil user login

        return view('my-profile', [
            'user' => $user
        ]);
    }

    public function updateAccount(Request $request)
    {
        // Ambil user yang sedang login sebagai model Eloquent
        $user = User::findOrFail(Auth::id());

        // Validasi input
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:4|confirmed',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update username
        $user->username = $validated['username'];

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        // Update foto jika upload baru
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('foto'), $fileName);
            $user->foto = $fileName;
        }

        // Simpan ke database
        $user->save();

        return back();
    }

    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function forgotCheckUser(Request $request)
    {
        $request->validate(['username' => 'required']);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan']);
        }

        // redirect ke GET route supaya halaman bisa diakses langsung / direfresh
        return redirect()->route('forgot-question', ['id' => $user->id]);
    }

    // new method — tampilkan form pertanyaan via GET
    public function forgotQuestionForm($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('forgot-password')->withErrors(['error' => 'User tidak ditemukan']);
        }
        return view('auth.forgot-question', ['user' => $user]);
    }

    public function forgotCheckAnswer(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'security_answer' => 'required'
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return back()->withErrors(['error' => 'User tidak ditemukan']);
        }

        $provided = trim($request->security_answer);
        $stored = $user->security_answer ?? '';
        $ok = false;

        if (Str::startsWith($stored, ['$2y$', '$2a$', '$2b$'])) {
            if (Hash::check($provided, $stored)) $ok = true;
        } else {
            if (mb_strtolower(trim($stored)) === mb_strtolower($provided)) $ok = true;
        }

        if (!$ok) {
            return back()->withErrors(['security_answer' => 'Jawaban salah']);
        }

        // redirect ke GET form reset agar URL sesuai
        return redirect()->route('forgot-reset-form', ['id' => $user->id]);
    }

    public function resetPasswordForm($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('forgot-password')->withErrors(['error' => 'User tidak ditemukan']);
        }

        return view('auth.reset-password', [
            'user' => $user
        ]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'password' => 'required|min:4|confirmed'
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return back()->withErrors(['error' => 'User tidak ditemukan']);
        }

        // Cek apakah password baru sama dengan password lama
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password tidak boleh sama dengan password sebelumnya.'
            ]);
        }

        // Simpan password lama ke kolom old_password
        $user->old_password = $user->password;

        // Simpan password baru
        $user->password = Hash::make($request->password);

        // Catat waktu password diganti
        $user->last_password_changed_at = now();

        $user->save();

        return redirect()->route('login')->with('success', 'Password berhasil direset');
    }

    // =======================
    // SIGN UP FORM
    // =======================
    public function signupForm()
    {
        // Ambil daftar security question dari config
        $questions = config('secure.security_questions');

        // Pilih secara acak
        $randomQuestion = collect($questions)->random();

        return view('auth.sign-up', [
            'title' => 'Daftar Akun',
            'question' => $randomQuestion,
        ]);
    }



    public function signupStore(Request $request, WhatsappService $waService)
    {
        $request->validate([
            'idnumber' => 'required',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|confirmed|min:4',
            'no_hp' => 'required|string|max:15',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
            'foto' => 'nullable|image',
        ], [
            'idnumber.required' => 'NIM atau NIP wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah dipakai.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'no_hp.required' => 'Nomor HP wajib diisi maksimal 15 karakter.',
            'security_question.required' => 'Pertanyaan keamanan wajib diisi.',
            'security_answer.required' => 'Jawaban keamanan wajib diisi.',
        ]);

        $id = trim($request->idnumber);
        $length = strlen($id);
        $last3 = substr($id, -3);

        $daftarNIM = config('secure.nims');
        $daftarNIP = config('secure.nips');
        $kodeInstansi = config('secure.institute_code');

        $used = User::pluck('nim')->merge(User::pluck('nip'))->toArray();
        if (in_array($id, $used)) {
            return back()->withErrors(['idnumber' => 'NIM atau NIP ini sudah digunakan.'])->withInput();
        }

        if ($length < 7) {
            return back()->withErrors(['idnumber' => 'NIM atau NIP terlalu pendek.'])->withInput();
        }

        if ($length >= 8 && $length <= 15) {
            return back()->withErrors(['idnumber' => 'NIM atau NIP tidak memenuhi syarat.'])->withInput();
        }

        if ($length > 16 && $length != 18) {
            return back()->withErrors(['idnumber' => 'Format NIM atau NIP tidak valid.'])->withInput();
        }

        // ================= ROLE DETECTION =================
        if ($length == 7) {
            if (!in_array($id, $daftarNIM)) {
                return back()->withErrors(['idnumber' => 'NIM tidak terdaftar.'])->withInput();
            }
            $role = 3;
            $nim = $id;
            $nip = null;
            $roleCode = 'MHS';
        } elseif ($length == 18) {
            if ($last3 != $kodeInstansi || !in_array($id, $daftarNIP)) {
                return back()->withErrors(['idnumber' => 'NIP tidak valid.'])->withInput();
            }
            $role = 2;
            $nim = null;
            $nip = $id;
            $roleCode = 'DSN';
        } else {
            return back()->withErrors(['idnumber' => 'Format NIM/NIP tidak valid.'])->withInput();
        }

        // ================= FOTO =================
        $fotoName = 'default.png';
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('foto'), $fotoName);
        }

        // ================= SIMPAN USER =================
        // normalisasi no hp
        $noHp = preg_replace('/[^0-9]/', '', $request->no_hp); // hapus karakter non-angka
        if (substr($noHp, 0, 1) === '0') {
            $noHp = '62' . substr($noHp, 1); // ganti 0 awal jadi 62
        }

        // simpan user
        $securityAnswerValue = $request->security_answer;
        $user = User::create([
            'nama' => $request->username,
            'nim' => $nim,
            'nip' => $nip,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'no_hp' => $noHp,
            'foto' => $fotoName,
            'security_question' => $request->security_question,
            'security_answer' => Hash::make($request->security_answer),
            'role_id' => $role
        ]);


        // ================= GENERATE QR =================
        // ================= GENERATE QR =================
        // token acak unik (10 karakter, uppercase)
        $token = strtoupper(Str::random(10));

        // format QR
        $qrCode = "USER-{$token}-{$roleCode}";

        // QR URL dengan background putih
        $qrUrl = 'https://bwipjs-api.metafloor.com/?bcid=qrcode'
            . '&text=' . urlencode($qrCode)
            . '&scale=6'
            . '&backgroundcolor=FFFFFF';

        $user->update([
            'qr_code' => $qrCode,
            'qr_url'  => $qrUrl,
        ]);


        // ================= NOTIF WHATSAPP =================
        if (!empty($user->no_hp) && !empty($user->qr_url)) {

            $tanggal = now('Asia/Jakarta')->format('d F Y');

            $roleText = match ($user->role_id) {
                2 => 'Dosen',
                3 => 'Mahasiswa',
                default => 'Pengguna',
            };

            $msg = "Halo {$user->nama}, akun {$roleText} berhasil dibuat.\n\n"
                . "Detail akun:\n"
                . ($user->nim ? "NIM: {$user->nim}\n" : "")
                . ($user->nip ? "NIP: {$user->nip}\n" : "")
                . "Username: {$user->username}\n"
                . "Pertanyaan Keamanan: {$user->security_question}\n"
                . "Jawaban Keamanan: {$securityAnswerValue}\n\n"
                . "QR Code akun anda (klik link berikut):\n"
                . "{$user->qr_url}\n"
                . "*Harap tidak membagikan informasi ini kepada siapa pun karena bersifat pribadi.*\n\n"
                . "QR Code ini berfungsi sebagai *identitas digital*.\n"
                . "Jika lupa password, *pemulihan akun* dapat dilakukan "
                . "dengan datang ke Ruang Teknisi dan menunjukkan *QR Code*.\n"
                . "*Pemulihan mandiri* juga tersedia di website TechNoteAPP.\n\n"
                . "_{$tanggal}_\n"
                . "_Sent via TechNoteAPP (powered by Green.com)_";

            $waService->sendMessage($user->no_hp, $msg);

        }



        return redirect()->route('login')->with('success', 'Akun berhasil dibuat.');
    }
}
