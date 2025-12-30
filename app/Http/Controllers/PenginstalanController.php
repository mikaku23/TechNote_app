<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\rekap;
use App\Models\software;
use Illuminate\Support\Str;
use App\Models\penginstalan;
use Illuminate\Http\Request;


class PenginstalanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status'); // hanya berhasil atau gagal
        $tanggal = $request->input('tanggal');

        $query = Penginstalan::with(['software', 'user'])
            ->whereNull('tgl_hapus'); // hanya data aktif (tidak dihapus)

        // 🔍 Search berdasarkan nama user
        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        // 🎯 Filter status (jika user pilih berhasil/gagal)
        if (!empty($status) && in_array($status, ['berhasil', 'gagal'])) {
            $query->where('status', $status);
        }

        // 📅 Filter berdasarkan tanggal instalasi
        if (!empty($tanggal)) {
            $query->whereDate('tgl_instalasi', $tanggal);
        }

        // Pagination
        $penginstalan = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('tgl_instalasi', 'desc')
            ->paginate(10)
            ->withQueryString();

        $jumlahTerhapus = Penginstalan::onlyTrashed()->count();


        return view('admin.penginstalan.index', [
            'menu' => 'penginstalan',
            'title' => 'Data Penginstalan',
            'penginstalan' => $penginstalan,
            'jumlahTerhapus' => $jumlahTerhapus,
        ]);
    }



    public function create()
    {
        $users = User::whereHas('role', fn($q) => $q->where('status', 'mahasiswa'))->get();
        $softwares = software::all();

        return view('admin.penginstalan.create', [
            'menu' => 'penginstalan',
            'title' => 'Tambah Data Penginstalan',
            'users' => $users,
            'softwares' => $softwares,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'estimasi' => 'nullable|date_format:H:i',
            'software_id' => 'required|exists:software,id',
            'user_id' => 'required|exists:users,id',
        ], [
            'estimasi.date_format' => 'Estimasi harus dalam format jam:menit.',
            'software_id.required' => 'Software harus dipilih.',
            'user_id.required' => 'Pengguna harus dipilih.',
        ]);
          

        // Simpan ke database
        $penginstalan = new penginstalan();
        $penginstalan->tgl_instalasi = now()->toDateString();
        $penginstalan->tgl_hapus = null;
        $penginstalan->status = 'pending';
        $penginstalan->estimasi = $validated['estimasi'] ?? null;
        $penginstalan->software_id = $validated['software_id'];
        $penginstalan->user_id = $validated['user_id'];
        $penginstalan->save();

        $rekap = new rekap();
        $rekap->penginstalan_id = $penginstalan->id;
        $rekap->status = 'tersedia';
        $rekap->save();
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('software.index');
    }

    public function edit($id)
    {
        $users = User::all();
        $softwares = software::all();
        $penginstalan = penginstalan::findOrFail($id);
        return view('admin.penginstalan.edit', [
            'menu' => 'penginstalan',
            'title' => 'Edit Data Penginstalan',
            'users' => $users,
            'softwares' => $softwares,
            'penginstalan' => $penginstalan,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'status' => 'nullable|in:berhasil,gagal',
            'estimasi' => 'nullable',
            'software_id' => 'nullable|exists:software,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Update di database
        $penginstalan = penginstalan::findOrFail($id);
        $penginstalan->status = $validated['status'] ?? null;
        $penginstalan->estimasi = $validated['estimasi'] ?? null;
        $penginstalan->software_id = $validated['software_id'] ?? null;
        $penginstalan->user_id = $validated['user_id'] ?? null;
        $penginstalan->save();

        // jika request AJAX/kirim JSON — kembalikan JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        // untuk normal form submit — redirect agar user melihat perubahan
        return redirect()->route('penginstalan.index')->with('success', 'Data berhasil diperbarui');
        
    }

    public function show($id)
    {
        $penginstalan = penginstalan::findOrFail($id);
        return view('admin.penginstalan.show', [
            'penginstalan' => $penginstalan
        ]);
    }

    public function hapusSemua()
    {
        try {
            // ambil semua yang belum dihapus
            $items = penginstalan::whereNull('tgl_hapus')->get();

            // hapus satu per satu agar trigger/observer/softdelete berjalan
            foreach ($items as $item) {
                $item->delete();

                // hapus/soft-delete rekaps terkait
                rekap::where('penginstalan_id', $item->id)->get()->each(fn($r) => $r->delete());
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        $penginstalan = penginstalan::findOrFail($id);

        // gunakan Eloquent delete() supaya Laravel mengisi tgl_hapus otomatis
        $penginstalan->delete();

        // tandai rekap terkait sebagai terhapus (soft delete)
        rekap::where('penginstalan_id', $id)->each(function ($r) {
            if (! $r->trashed()) {
                $r->delete();
            }
        });

        return redirect()->route('penginstalan.index');
    }



    public function arsip()
    {
        $penginstalan = Penginstalan::onlyTrashed()
            ->with(['software', 'user'])
            ->orderBy('tgl_hapus', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.penginstalan.recycle', [
            'menu' => 'penginstalan',
            'title' => 'Data Terhapus Instalasi Software',
            'penginstalan' => $penginstalan,
        ]);
    }

    public function pulihkan($id)
    {
        try {
            // Ambil data penginstalan, termasuk yang sudah di-soft delete
            $penginstalan = Penginstalan::withTrashed()->findOrFail($id);

            // Pulihkan penginstalan
            $penginstalan->restore(); // karena sudah pakai SoftDeletes
            $penginstalan->status = 'berhasil';
            $penginstalan->tgl_hapus = null;
            $penginstalan->save();

            // Pulihkan semua rekap yang terkait (jika ada)
            Rekap::onlyTrashed()
                ->where('penginstalan_id', $id)
                ->restore();

            // Update status rekap setelah dipulihkan
            Rekap::where('penginstalan_id', $id)->update(['status' => 'tersedia']);

            return redirect()
                ->route('penginstalan.arsip')
                ->with('success', 'Data berhasil dipulihkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:berhasil,gagal,pending',
        ]);

        $penginstalan = Penginstalan::findOrFail($id);
        $penginstalan->status = $request->status;
        $penginstalan->save();

        return back()->with('message', 'Status berhasil diperbarui');
    }
}
