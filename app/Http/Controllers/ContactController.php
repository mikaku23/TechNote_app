<?php

namespace App\Http\Controllers;

use App\Models\contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'pesan' => 'required|string',
        ]);

        // Simpan data kontak ke database
        contact::create($validatedData);

        // Redirect kembali dengan pesan sukses
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
        ]);

        // Simpan data kontak ke database
        contact::create($validatedData);

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with([
            'message' => 'Pesan Anda telah terkirim. Terima kasih!',
            'alert' => 'success'
        ]);
    }
}
