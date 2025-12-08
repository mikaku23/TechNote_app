<?php

namespace App\Http\Controllers;


use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleId = $request->input('role');
        $tanggal = $request->input('tanggal'); // pastikan name input di blade: name="tanggal"

        $query = User::with('role');

        // Filter pencarian nama / username
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%');
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


    public function store(Request $request)
    {

        $nm = $request->foto;
        $namaFile = $nm->getClientOriginalName();

        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'nullable|string|unique:users,nim',
            'nip' => 'nullable|string|unique:users,nip',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'security_question' => 'nullable|string|max:255',
            'security_answer' => 'nullable|string|max:255',
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
        $user->security_question = $validated['security_question'];
        $user->security_answer = bcrypt($validated['security_answer']);
        $user->foto = $namaFile;
        $user->role_id = $validated['role_id'];

        $nm->move(public_path() . '/foto', $namaFile);
        $user->save();

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

    public function storeMahasiswa(Request $request)
    {
        $nm = $request->foto;
        $namaFile = $nm->getClientOriginalName();
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'required|string|unique:users,nim',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        $user->security_question = $validated['security_question'];
        $user->security_answer = bcrypt($validated['security_answer']);
        $user->foto = $namaFile;
        $user->role_id = 3; // role_id otomatis mahasiswa

        $nm->move(public_path() . '/foto', $namaFile);
        $user->save();

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

    public function storeDosen(Request $request)
    {
        $nm = $request->foto;
        $namaFile = $nm->getClientOriginalName();
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|string|unique:users,nip',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        $user->security_question = $validated['security_question'];
        $user->security_answer = bcrypt($validated['security_answer']);
        $user->foto = $namaFile;
        $user->role_id = 2; // role_id otomatis dosen

        $nm->move(public_path() . '/foto', $namaFile);
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

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->forceDelete(); // hapus permanen

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
            User::withTrashed()->forceDelete(); // hapus permanen semua
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
