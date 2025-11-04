<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        $datauser = User::all();
        return view('admin.pengguna.index', [
            'menu' => 'pengguna',
            'title' => 'Data user',
            'datauser' => $datauser
        ]);
    }
    public function create()
    {
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
        ]);
    }


    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'nullable|string|unique:users,nim',
            'nip' => 'nullable|string|unique:users,nip',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'role_id' => 'required|exists:roles,id',
        ], [
            'nama.required' => 'Nama pengguna harus diisi.',
            'nim.required' => 'nim harus diisi.',
            'nim.unique' => 'nim sudah digunakan.',
            'nip.required' => 'nip harus diisi.',
            'nip.unique' => 'nip sudah digunakan.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'role_id.required' => 'Silakan pilih role pengguna.',
            'role_id.exists' => 'Role tidak valid.',
        ]);

        // Simpan ke database
        $user = new User();
        $user->nama = $validated['nama'];
        $user->nim = $validated['nim'] ?? null;
        $user->nip = $validated['nip'] ?? null;
        $user->username = $validated['username'];
        $user->password = bcrypt($validated['password']);
        $user->role_id = $validated['role_id'];
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengguna.index');
    }

    public function createMahasiswa()
    {
        return view('admin.pengguna.create_mahasiswa', [
            'menu' => 'pengguna',
            'title' => 'Tambah Mahasiswa',
        ]);
    }

    public function storeMahasiswa(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'required|string|unique:users,nim',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
        ], [
            'nama.required' => 'Nama pengguna harus diisi.',
            'nim.required' => 'NIM harus diisi.',
            'nim.unique' => 'NIM sudah digunakan.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 4 karakter.',
        ]);

        // Simpan ke database
        $user = new User();
        $user->nama = $validated['nama'];
        $user->nim = $validated['nim'] ?? null;
        $user->username = $validated['username'];
        $user->password = bcrypt($validated['password']);
        $user->role_id = 3; // role_id otomatis mahasiswa
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengguna.index');
    }


    public function createDosen()
    {
        return view('admin.pengguna.create_dosen', [
            'menu' => 'pengguna',
            'title' => 'Tambah Dosen',
        ]);
    }

    public function storeDosen(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|string|unique:users,nip',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
        ], [
            'nama.required' => 'Nama pengguna harus diisi.',
            'nip.required' => 'NIP harus diisi.',
            'nip.unique' => 'NIP sudah digunakan.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 4 karakter.',
        ]);

        // Simpan ke database
        $user = new User();
        $user->nama = $validated['nama'];
        $user->nip = $validated['nip'] ?? null;
        $user->username = $validated['username'];
        $user->password = bcrypt($validated['password']);
        $user->role_id = 2; // role_id otomatis dosen
        $user->save();

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
        // Validasi input
        $validated = $request->validate([
            'nama' => 'nullable',
            'nim' => 'nullable',
            'nip' => 'nullable',
            'username' => 'nullable',
            'password' => 'nullable',
            'role_id' => 'nullable',
        ]);

        // Update data di database
        $user = User::findOrFail($id);
        $user->nama = $validated['nama'];
        $user->nim = $validated['nim'] ?? null;
        $user->nip = $validated['nip'] ?? null;
        $user->username = $validated['username'];
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->role_id = $validated['role_id'];
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('pengguna.index');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('pengguna.index');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.pengguna.show', [
            'menu' => 'pengguna',
            'title' => 'Show Pengguna',
            'user' => $user,
            'roles' => $roles,
        ]);
    }
}
