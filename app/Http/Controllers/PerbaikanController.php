<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\rekap;
use App\Models\login_log;
use App\Models\perbaikan;
use Illuminate\Support\Str;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


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

        $perbaikan = $query
            ->orderByRaw("CASE WHEN status = 'sedang diperbaiki' THEN 0 ELSE 1 END")
            ->orderBy('tgl_perbaikan', 'desc')
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
        $user = User::whereHas('role', fn($q) => $q->where('status', 'dosen'))->get();
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
                    'activity'     => 'Menambahkan data perbaikan baru dengan id: ' . $perbaikan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }
        
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
        ], [
            'status.in' => 'Status harus berupa sedang diperbaiki, rusak, selesai, atau bagus.',
            'user_id.exists' => 'Pengguna tidak valid.',
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
                    'activity'     => 'Mengupdate data perbaikan dengan id: ' . $perbaikan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $perbaikan = perbaikan::findOrFail($id);

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
                    'activity'     => 'Melihat data perbaikan dengan id: ' . $perbaikan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

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
                    'activity'     => 'Menghapus data perbaikan dengan id: ' . $perbaikan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

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
                        'activity'     => 'Memulihkan data perbaikan dengan id: ' . $perbaikan->id,
                        'type' => 'nonsistem',
                        'created_at'   => now('Asia/Jakarta'),
                    ]);
                }
            }

            return redirect()
                ->route('perbaikan.arsip')
                ->with('success', 'Data berhasil dipulihkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }



    public function updateStatus(Request $request, $id, WhatsappService $waService)
    {
        $request->validate([
            'status' => 'required|in:rusak,sedang diperbaiki,selesai,bagus',
        ]);

        $perbaikan = Perbaikan::with('user')->findOrFail($id);
        $perbaikan->status = $request->status;
        $perbaikan->save();

        if (in_array($request->status, ['selesai', 'rusak'])) {

            $nomor = str_pad($perbaikan->id, 6, '0', STR_PAD_LEFT);
            $hasil = $request->status === 'selesai' ? 'SUCCESS' : 'FAILED';

            $qrCode = "REPAIR-{$nomor}-{$hasil}";

            $perbaikan->update([
                'qr_code' => $qrCode,
                'qr_url'  => 'https://bwipjs-api.metafloor.com/?bcid=qrcode&text='
                    . urlencode($qrCode)
                    . '&scale=6'
            ]);

            $perbaikan->refresh();
        }


        // Kirim WA jika status selesai atau gagal (anggap 'rusak' = gagal)
        if (($perbaikan->status === 'selesai' || $perbaikan->status === 'rusak')
            && $perbaikan->user?->no_hp
            && !$perbaikan->notif_terkirim
        ) {
            // Durasi pengerjaan (opsional, jika ada estimasi atau start)
            $mulai = $perbaikan->created_at instanceof Carbon
                ? $perbaikan->created_at->copy()
                : Carbon::parse($perbaikan->created_at)->setTimezone('Asia/Jakarta');

            $sekarang = Carbon::now('Asia/Jakarta');
            $durasi = $mulai->diffInMinutes($sekarang);
            $jam = floor($durasi / 60);
            $menit = $durasi % 60;
            $durasiText = ($jam > 0 ? $jam . ' jam ' : '') . $menit . ' menit';

            $tanggalSelesai = $perbaikan->tgl_perbaikan
                ? Carbon::parse($perbaikan->tgl_perbaikan)->setTimezone('Asia/Jakarta')->format('d F Y')
                : 'tidak ada data';

            // Tentukan pesan berdasarkan status
            if ($perbaikan->status === 'selesai' || $perbaikan->status === 'bagus') {
                $msg = "Halo {$perbaikan->user->nama}, perbaikan {$perbaikan->nama} anda telah *selesai*.\n\n"
                    . "Berikut data perbaikan anda:\n"
                    . "Nama barang: {$perbaikan->nama}\n"
                    . "Kategori: {$perbaikan->kategori}\n"
                    . "Status: {$perbaikan->status}\n"
                    . "Durasi pengerjaan: {$durasiText}\n\n"
                    . "Silakan datang ke ruang teknisi untuk mengambil barang.\n\n"
                    . "_{$tanggalSelesai}_\n"
                    . "_Sent via TechNoteAPP (powered by Green.com)_";
            } elseif ($perbaikan->status === 'rusak') {
                $msg = "Halo {$perbaikan->user->nama}, perbaikan {$perbaikan->nama} anda *gagal* karena ada masalah teknis.\n\n"
                    . "Berikut data perbaikan anda:\n"
                    . "Nama barang: {$perbaikan->nama}\n"
                    . "Kategori: {$perbaikan->kategori}\n"
                    . "Status: {$perbaikan->status}\n\n"
                    . "Silakan datang ke ruang teknisi untuk informasi lebih lanjut.\n\n"
                    . "_{$tanggalSelesai}_\n"
                    . "_Sent via TechNoteAPP (powered by Green.com)_";
            }

            if ($waService->sendMessage($perbaikan->user->no_hp, $msg)) {
                $perbaikan->update(['notif_terkirim' => true]);
            }

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
                        'activity'     => 'Dikirimkan notifikasi WhatsApp tentang perbaikan telah selesai dengan idperbaikan: ' . $perbaikan->id,
                        'type'         => 'sistem',
                        'created_at'   => now('Asia/Jakarta'),
                    ]);
                }
            }
        }

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
                    'activity'     => 'Mengupdate status data perbaikan dengan id: ' . $perbaikan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        return back()->with('message', 'Status berhasil diperbarui');
    }
}