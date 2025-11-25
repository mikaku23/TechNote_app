<?php

namespace App\Http\Controllers;

use App\Models\software;
use Illuminate\Http\Request;

class SoftwareController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Ambil daftar developer unik untuk dropdown
        $developers = Software::select('developer')
            ->whereNotNull('developer')
            ->where('developer', '!=', '')
            ->distinct()
            ->orderBy('developer', 'asc')
            ->get();

        $developer = $request->input('developer');
        $tanggal = $request->input('tanggal');

        $query = Software::query();

        // Search berdasarkan nama
        if (!empty($search)) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        // Filter developer
        if (!empty($developer)) {
            $query->where('developer', $developer);
            // pakai "=" saja, tidak perlu like
            // kalau ingin ketik manual baru pakai like
        }

        // Filter tanggal rilis
        if (!empty($tanggal)) {
            $query->whereDate('tgl_rilis', '=', $tanggal);
        }

        $software = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.software.index', [
            'menu' => 'software',
            'title' => 'Data Software',
            'software' => $software,
            'developers' => $developers, // WAJIB dikirim ke view
        ]);
    }




    public function create()
    {
        return view('admin.software.create', [
            'menu' => 'software',
            'title' => 'Tambah Software',
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'versi' => 'required|string|max:100',
            'kategori' => 'nullable|string|max:100',
            'lisensi' => 'nullable|string|max:100',
            'developer' => 'nullable|string|max:100',
            'tgl_rilis' => 'nullable|date',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama software harus diisi.',
            'versi.required' => 'Versi software harus diisi.',
            
            
        ]);

        // Simpan ke database
        $software = new software();
        $software->nama = $validated['nama'];
        $software->versi = $validated['versi'];
        $software->kategori = $validated['kategori'] ?? null;
        $software->lisensi = $validated['lisensi'] ?? null;
        $software->developer = $validated['developer'] ?? null;
        $software->tgl_rilis = $validated['tgl_rilis'] ?? null;
        $software->deskripsi = $validated['deskripsi'] ?? null;
        $software->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('software.index');
    }

    public function show($id)
    {
        $software = software::findOrFail($id);
        return view('admin.software.show', [
            'software' => $software
        ]);
    }

    public function destroy($id)
    {
        $software = software::findOrFail($id);
        $software->delete();

        return redirect()
            ->route('software.index');
    }

    public function edit($id)
    {
       $software = software::findOrFail($id);
        return view('admin.software.edit', [
            'menu' => 'software',
            'title' => 'Edit Software',
            'software' => $software,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'nullable|string',
            'versi' => 'nullable|string',
            'kategori' => 'nullable|string',
            'lisensi' => 'nullable|string',
            'developer' => 'nullable|string',
            'tgl_rilis' => 'nullable|date',
            'deskripsi' => 'nullable|string',
        ]);

        $software = Software::findOrFail($id);

        $software->fill([
            'nama' => $validated['nama'] ?? null,
            'versi' => $validated['versi'] ?? null,
            'kategori' => $validated['kategori'] ?? null,
            'lisensi' => $validated['lisensi'] ?? null,
            'developer' => $validated['developer'] ?? null,
            'tgl_rilis' => $validated['tgl_rilis']
                ? \Carbon\Carbon::parse($validated['tgl_rilis'])->format('Y-m-d')
                : null,
            'deskripsi' => $validated['deskripsi'] ?? null,
        ])->save();

        return response()->json(['success' => true]);
    }
    public function hapusSemua()
    {
        try {
            software::query()->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
