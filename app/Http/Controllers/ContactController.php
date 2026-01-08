<?php

namespace App\Http\Controllers;

use App\Models\contact;
use App\Models\login_log;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'pesan' => 'required|string',
        ], [
            'nama.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'pesan.required' => 'Pesan harus diisi.',
        ]);

        // Ambil id user yang login
        $validatedData['user_id'] = Auth::user()->id;

        // Simpan data
        Contact::create($validatedData);

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
                    'activity'     => 'Mahasiswa mengirim data contact baru',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        // Redirect kembali
        return redirect()->back()->with([
            'message' => 'Pesan Anda telah terkirim. Terima kasih!',
            'alert' => 'success'
        ]);
    }
    public function submitDosen(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'pesan' => 'required|string',
        ], [
            'nama.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'pesan.required' => 'Pesan harus diisi.',
        ]);
        // Ambil id user yang login
        $validatedData['user_id'] = Auth::user()->id;
        
        // Simpan data kontak ke database
        contact::create($validatedData);

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
                    'activity'     => 'Dosen mengirim data contact baru',
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with([
            'message' => 'Pesan Anda telah terkirim. Terima kasih!',
            'alert' => 'success'
        ]);
    }

    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->is_read = true;
        $contact->save();

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
                    'activity'     => 'Membaca pesan contact dengan nama ' . $contact->user->nama . ' dan role ' . $contact->user->role->status, 
                    'created_at'   => now('Asia/Jakarta'),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
