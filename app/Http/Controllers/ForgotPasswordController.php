<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordResetOtp;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function showChoice()
    {
        return view('auth.forgot.choose'); // lihat blade di bawah
    }

    public function showPhoneForm()
    {
        return view('auth.forgot.phone'); // form input no_hp
    }

    public function sendOtpByPhone(Request $request, WhatsappService $waService)
    {
        $request->validate([
            'no_hp' => 'required'
        ]);

        /*
    |--------------------------------------------------------------------------
    | NORMALISASI NOMOR HP
    |--------------------------------------------------------------------------
    | 08xxxxxxxx   -> 628xxxxxxxx
    | +62xxxxxxxx  -> 62xxxxxxxx
    | 62xxxxxxxx   -> 62xxxxxxxx
    */
        $rawPhone = preg_replace('/[^0-9]/', '', $request->no_hp);

        if (str_starts_with($rawPhone, '0')) {
            $phone = '62' . substr($rawPhone, 1);
        } elseif (str_starts_with($rawPhone, '62')) {
            $phone = $rawPhone;
        } else {
            return back()->withErrors([
                'no_hp' => 'Format nomor HP tidak valid'
            ]);
        }

        // CARI USER (SATU KALI SAJA)
        $user = User::where('no_hp', $phone)->first();

        if (!$user) {
            return back()->withErrors([
                'no_hp' => 'Nomor HP tidak terdaftar'
            ]);
        }

        // HAPUS OTP LAMA
        PasswordResetOtp::where('user_id', $user->id)->delete();

        // GENERATE OTP
        $otp = random_int(10000, 99999);

        $otpModel = PasswordResetOtp::create([
            'user_id'    => $user->id,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => Carbon::now('Asia/Jakarta')->addMinutes(10),
        ]);

        // PESAN WA
        $msg =
            "Halo {$user->nama},\n\n" .
            "Kode OTP reset password TechNoteApp:\n" .
            "*{$otp}*\n\n" .
            "Kode berlaku 10 menit.\n\n" .
            "_Sent via TechNoteAPP_";

        // KIRIM WA
        $sent = $waService->sendMessage($phone, $msg);

        if (!$sent) {
            // rollback OTP
            $otpModel->delete();

            return back()->withErrors([
                'no_hp' => 'Gagal mengirim OTP ke WhatsApp'
            ]);
        }

        return redirect()
            ->route('forgot.phone.verify', $otpModel->id)
            ->with('status', 'Kode OTP telah dikirim ke WhatsApp');
    }



    public function showVerifyForm($otpId)
    {
        return view('auth.forgot.verify')->with(['otpId' => $otpId]);
    }

    public function verifyOtp(Request $request)
    {
        $v = Validator::make($request->all(), [
            'otp_id' => 'required|integer',
            'otp' => 'required|string|size:5',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $otpId = $request->otp_id;
        $inputOtp = $request->otp;

        $record = PasswordResetOtp::find($otpId);

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid']);
        }

        if ($record->used) {
            return back()->withErrors(['otp' => 'Kode OTP sudah digunakan']);
        }

        if (Carbon::now('Asia/Jakarta')->greaterThan($record->expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa']);
        }

        // Batasi jumlah percobaan (mis. 5)
        if ($record->attempts >= 5) {
            $record->used = true;
            $record->save();
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan, OTP diblokir']);
        }

        // Cek OTP (hash)
        if (!Hash::check($inputOtp, $record->otp_hash)) {
            $record->attempts = $record->attempts + 1;
            $record->save();
            return back()->withErrors(['otp' => 'Kode OTP salah']);
        }

        // Berhasil
        $record->used = true;
        $record->save();

        // Simpan session sementara
        session(['password_reset_user_id' => $record->user_id]);

        return redirect()->route('forgot.phone.reset');
    }

    public function showResetForm()
    {
        if (!session('password_reset_user_id')) {
            return redirect()->route('forgot.choose')->withErrors(['error' => 'Sesi reset tidak ditemukan']);
        }

        return view('auth.forgot.reset');
    }

    public function resetPassword(Request $request)
    {
        $v = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        if ($v->fails()) {
            return back()->withErrors($v);
        }

        $userId = session('password_reset_user_id');
        if (!$userId) {
            return redirect()->route('forgot.choose')->withErrors(['error' => 'Sesi reset tidak ditemukan']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('forgot.choose')->withErrors(['error' => 'User tidak ditemukan']);
        }

        $user->password = Hash::make($request->password);
        $user->last_password_changed_at = Carbon::now('Asia/Jakarta');
        $user->save();

        // Hapus semua otp user
        PasswordResetOtp::where('user_id', $user->id)->delete();

        // Hapus session
        session()->forget('password_reset_user_id');

        return redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan login.');
    }
}
