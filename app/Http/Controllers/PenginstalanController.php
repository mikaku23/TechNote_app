<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\rekap;
use App\Models\software;
use App\Models\login_log;
use Illuminate\Support\Str;
use App\Models\penginstalan;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Auth;


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
                    'activity'     => 'Menambahkan data penginstalan baru dengan id: ' . $penginstalan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }
        
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
        ], [
            'status.in' => 'Status harus berupa berhasil atau gagal.',
            'software_id.exists' => 'Software tidak valid.',
            'user_id.exists' => 'Pengguna tidak valid.',
        ]);

        // Update di database
        $penginstalan = penginstalan::findOrFail($id);
        $penginstalan->status = $validated['status'] ?? null;
        $penginstalan->estimasi = $validated['estimasi'] ?? null;
        $penginstalan->software_id = $validated['software_id'] ?? null;
        $penginstalan->user_id = $validated['user_id'] ?? null;
        $penginstalan->save();

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
                    'activity'     => 'Mengupdate data penginstalan dengan id: ' . $penginstalan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

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
                    'activity'     => 'Melihat data penginstalan dengan id: ' . $penginstalan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

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
                    'activity'     => 'Menghapus data penginstalan dengan id: ' . $penginstalan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

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
                        'activity'     => 'Memulihkan data penginstalan dengan id: ' . $penginstalan->id,
                        'type' => 'nonsistem',
                        'created_at'   => now('Asia/Jakarta'),
                    ]);
                }
            }

            return redirect()
                ->route('penginstalan.arsip')
                ->with('success', 'Data berhasil dipulihkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }



    public function updateStatus(Request $request, $id, WhatsappService $waService)
    {
        $request->validate([
            'status' => 'required|in:berhasil,gagal,pending',
        ]);

        $penginstalan = Penginstalan::with(['user', 'software'])->findOrFail($id);

        $penginstalan->status = $request->status;
        $penginstalan->save();

        if (in_array($request->status, ['berhasil', 'gagal'])) {

            $nomor = str_pad($penginstalan->id, 6, '0', STR_PAD_LEFT);
            $hasil = $request->status === 'berhasil' ? 'SUCCESS' : 'FAILED';

            $qrCode = "INST-{$nomor}-{$hasil}";

            $penginstalan->update([
                'qr_code' => $qrCode,
                'qr_url'  => 'https://bwipjs-api.metafloor.com/?bcid=qrcode&text='
                    . urlencode($qrCode)
                    . '&scale=6'
            ]);

            $penginstalan->refresh();
        }



        // kirim WA jika status BERHASIL atau GAGAL, dan belum pernah kirim
        if (
            in_array($request->status, ['berhasil', 'gagal']) &&
            !$penginstalan->notif_terkirim &&
            $penginstalan->user?->no_hp
        ) {

            // Hitung durasi
            $mulai = $penginstalan->created_at instanceof Carbon
                ? $penginstalan->created_at->copy()
                : Carbon::parse($penginstalan->created_at)->setTimezone('Asia/Jakarta');

            $parts = explode(':', $penginstalan->estimasi);
            $hour = isset($parts[0]) ? (int)$parts[0] : 0;
            $minute = isset($parts[1]) ? (int)$parts[1] : 0;
            $second = isset($parts[2]) ? (int)$parts[2] : 0;

            $estimasiDetik = $hour * 3600 + $minute * 60 + $second;
            $target = $mulai->copy()->addSeconds($estimasiDetik);
            $sekarang = Carbon::now('Asia/Jakarta');

            if ($sekarang->greaterThanOrEqualTo($target)) {
                $durasi = $mulai->diffInMinutes($target);
            } else {
                $durasi = $sekarang->diffInMinutes($target);
            }

            $jam = floor($durasi / 60);
            $menit = $durasi % 60;

            $durasiText = ($jam > 0 ? $jam . ' jam ' : '') . $menit . ' menit';

            $tanggalSelesai = $penginstalan->tgl_instalasi
                ? Carbon::parse($penginstalan->tgl_instalasi)->setTimezone('Asia/Jakarta')->format('d F Y')
                : 'tidak ada data';

            // Tentukan pesan berbeda jika gagal
            if ($request->status === 'berhasil') {
                $msg = "Halo {$penginstalan->user->nama}, penginstalan anda telah *selesai* dikerjakan\n\n"
                    . "Berikut data penginstalan anda:\n"
                    . "Nama software: {$penginstalan->software->nama}\n"
                    . "Versi: {$penginstalan->software->versi}\n"
                    . "Status penginstalan: {$penginstalan->status}\n"
                    . "Durasi Pengerjaan: {$durasiText}\n\n"
                    . "Silakan datang ke ruang teknisi untuk mengambil perangkat.\n\n"
                    . "_{$tanggalSelesai}_\n"
                    . "_Sent via TechNoteAPP (powered by Green.com)_";
            } else {
                // status gagal
                $msg = "Halo {$penginstalan->user->nama}, penginstalan anda *gagal* karena ada kesalahan teknis.\n\n"
                    . "Berikut data penginstalan anda:\n"
                    . "Nama software: {$penginstalan->software->nama}\n"
                    . "Versi: {$penginstalan->software->versi}\n"
                    . "Status penginstalan: {$penginstalan->status}\n\n"
                    . "Silakan datang ke ruang teknisi untuk informasi lebih lanjut.\n\n"
                    . "_{$tanggalSelesai}_\n"
                    . "_Sent via TechNoteAPP (powered by Green.com)_";
            }

            // Kirim WA
            if ($waService->sendMessage($penginstalan->user->no_hp, $msg)) {
                $penginstalan->update(['notif_terkirim' => true]);
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
                        'activity'     => 'Dikirimkan notifikasi WhatsApp tentang penginstalan telah selesai dengan idpenginstalan: ' . $penginstalan->id,
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
                    'activity'     => 'update status penginstalan dengan id: ' . $penginstalan->id,
                    'type' => 'nonsistem',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }
        return back()->with('message', 'Status berhasil diperbarui');
    }
}
