<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


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



    public function signupStore(Request $request)
    {
        $request->validate([
            'idnumber' => 'required',
            'nama' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|confirmed|min:4',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
            'foto' => 'nullable|image',
        ], [
            // Pesan validasi keren
            'idnumber.required' => 'NIM atau NIP wajib diisi.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah dipakai, buat username lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'security_question.required' => 'Pertanyaan keamanan wajib diisi.',
            'security_answer.required' => 'Jawaban keamanan wajib diisi.',
            'foto.image' => 'File foto harus berupa gambar.',
        ]);

        $id = trim($request->idnumber);
        $length = strlen($id);
        $last3 = substr($id, -3);

        // DATA CONFIG
        $daftarNIM = config('secure.nims');
        $daftarNIP = config('secure.nips');
        $kodeInstansi = config('secure.institute_code');

        // CEK DUPLIKAT
        $used = User::pluck('nim')->merge(User::pluck('nip'))->toArray();

        if (in_array($id, $used)) {
            return back()->withErrors([
                'idnumber' => 'NIM atau NIP ini sudah digunakan.'
            ])->withInput();
        }

        // =================-------
        // CEK LEN NIM/NIP CUSTOM
        // =================-------
        if ($length < 7) {
            return back()->withErrors([
                'idnumber' => 'NIM atau NIP terlalu pendek.'
            ])->withInput();
        }

        // Jika panjang 8–15 → error
        if ($length >= 8 && $length <= 15) {
            return back()->withErrors([
                'idnumber' => 'NIM atau NIP tidak memenuhi syarat.'
            ])->withInput();
        }

        // Jika panjang > 16 → error
        if ($length > 16 && $length != 18) {
            // 18 = NIP dosen, valid
            return back()->withErrors([
                'idnumber' => 'NIM atau NIP terlalu panjang dan tidak sesuai format.'
            ])->withInput();
        }

        // =======================
        // DETEKSI MAHASISWA / DOSEN
        // =======================

        // Mahasiswa = 7 digit
        if ($length == 7) {
            if (!in_array($id, $daftarNIM)) {
                return back()->withErrors(['idnumber' => 'NIM tidak terdaftar sebagai mahasiswa.'])->withInput();
            }

            $role = 3;
            $nim = $id;
            $nip = null;
        }
        // Dosen = 18 digit
        elseif ($length == 18) {

            if ($last3 != $kodeInstansi) {
                return back()->withErrors(['idnumber' => 'Pastikan NIP anda benar.'])->withInput();
            }

            if (!in_array($id, $daftarNIP)) {
                return back()->withErrors(['idnumber' => 'NIP tidak terdaftar sebagai dosen.'])->withInput();
            }

            $role = 2;
            $nim = null;
            $nip = $id;
        } else {
            return back()->withErrors(['idnumber' => 'Format NIM/NIP tidak valid.'])->withInput();
        }

        // =======================
        // SIMPAN FOTO (JIKA ADA)
        // =======================
        $fotoName = 'default.png'; // foto default

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('foto'), $fotoName);
        }


        // =======================
        // SIMPAN USER
        // =======================
        User::create([
            'nama' => $request->nama,
            'nim' => $nim,
            'nip' => $nip,
            'username' => $request->username,
            'password' => $request->password,
            'foto' => $fotoName,
            'security_question' => $request->security_question,
            'security_answer' => $request->security_answer,
            'role_id' => $role
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat.');
    }
}
