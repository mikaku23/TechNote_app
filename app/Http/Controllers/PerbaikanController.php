<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\rekap;
use App\Models\perbaikan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PerbaikanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $lokasi = $request->input('lokasi');
        $tanggal = $request->input('tanggal');

        $query = Perbaikan::whereNull('deleted_at'); // hanya data aktif

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }


        // 🎯 Filter status
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // 📅 Filter tanggal perbaikan
        if (!empty($tanggal)) {
            $query->whereDate('tgl_perbaikan', $tanggal);
        }

        $perbaikan = $query->orderBy('tgl_perbaikan', 'desc')
            ->paginate(10)
            ->withQueryString();

        $jumlahTerhapus = Perbaikan::onlyTrashed()->count();

        return view('admin.perbaikan.index', [
            'menu' => 'perbaikan',
            'title' => 'Data Perbaikan',
            'perbaikan' => $perbaikan,
            'jumlahTerhapus' => $jumlahTerhapus,
        ]);
    }


    public function create()
    {
        $user = User::all();
        return view('admin.perbaikan.create', [
            'menu' => 'perbaikan',
            'title' => 'Tambah Data Perbaikan',
            'users' => $user,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'estimasi' => 'required|date_format:H:i',
            'user_id' => 'required|exists:users,id',
        ], [
            'nama.required' => 'Nama harus diisi.',
            'kategori.required' => 'Kategori harus diisi.',
            'lokasi.required' => 'Lokasi harus diisi.',
            'keterangan.required' => 'Keterangan harus diisi.',
            'estimasi.required' => 'Estimasi harus diisi.',
            'estimasi.date_format' => 'Format estimasi harus HH:MM.',
            'user_id.required' => 'Pengguna harus dipilih.',
            'user_id.exists' => 'Pengguna tidak ditemukan.',
        ]);

        // Simpan data perbaikan
        $perbaikan = new perbaikan();
        $perbaikan->nama = $validated['nama'];
        $perbaikan->kategori = $validated['kategori'];
        $perbaikan->lokasi = $validated['lokasi'];
        $perbaikan->status = 'sedang diperbaiki';
        $perbaikan->keterangan = $validated['keterangan'];
        $perbaikan->tgl_perbaikan = now()->toDateString();
        $perbaikan->estimasi = $validated['estimasi'];
        $perbaikan->user_id = $validated['user_id'];
        $perbaikan->save();

        $rekap = new rekap();
        $rekap->perbaikan_id = $perbaikan->id;
        $rekap->status = 'tersedia';
        $rekap->save();
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('perbaikan.index');
    }

    public function edit($id)
    {
        $users = User::all();
        $perbaikan = perbaikan::findOrFail($id);

        return view('admin.perbaikan.edit', [
            'menu' => 'perbaikan',
            'title' => 'Edit Data Perbaikan',
            'perbaikan' => $perbaikan,
            'users' => $users,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:sedang diperbaiki,rusak,selesai,bagus',
            'keterangan' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Update di database
        $perbaikan = perbaikan::findOrFail($id);
        $perbaikan->nama = $validated['nama'] ?? null;
        $perbaikan->kategori = $validated['kategori'] ?? null;
        $perbaikan->lokasi = $validated['lokasi'] ?? null;
        $perbaikan->status = $validated['status'] ?? null;
        $perbaikan->keterangan = $validated['keterangan'] ?? null;
        $perbaikan->tgl_perbaikan = now()->toDateString();
        $perbaikan->user_id = $validated['user_id'] ?? null;
        $perbaikan->save();

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $perbaikan = perbaikan::findOrFail($id);
        return view('admin.perbaikan.show', [
            'perbaikan' => $perbaikan
        ]);
    }

    public function hapusSemua()
    {
        try {
            $items = Perbaikan::whereNull('deleted_at')->get();

            foreach ($items as $item) {
                $item->update(['status' => 'dihapus']);
                $item->delete(); // rekaps terkait ikut soft delete otomatis
            }

            return response()->json(['success' => true, 'message' => 'Semua data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    public function destroy($id)
    {
        $perbaikan = Perbaikan::findOrFail($id);

        // Tandai status sebelum dihapus
        $perbaikan->update(['status' => 'dihapus']);

        // Soft delete (rekap terkait akan otomatis ikut soft delete via model booted)
        $perbaikan->delete();

        return redirect()->route('perbaikan.index')
            ->with('success', 'Data berhasil dihapus.');
    }





    public function arsip()
    {
        $perbaikan = Perbaikan::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.perbaikan.recycle', [
            'menu' => 'perbaikan',
            'title' => 'Data Terhapus Perbaikan',
            'perbaikan' => $perbaikan,
        ])->with('success', 'Data berhasil dipulihkan.');
    }

    public function pulihkan($id)
    {
        try {
            $perbaikan = Perbaikan::withTrashed()->findOrFail($id);
            $perbaikan->restore();
            $perbaikan->update(['status' => 'berhasil']);

            return redirect()
                ->route('perbaikan.arsip')
                ->with('success', 'Data berhasil dipulihkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }



    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:rusak,sedang diperbaiki,selesai,bagus',
        ]);

        $perbaikan = Perbaikan::findOrFail($id);
        $perbaikan->status = $request->status;
        $perbaikan->save();

        return back()->with('message', 'Status berhasil diperbarui');
    }
}