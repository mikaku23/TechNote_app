<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // normalisasi no hp
        $noHp = '082285926175';
        $noHp = preg_replace('/[^0-9]/', '', $noHp);
        if (substr($noHp, 0, 1) === '0') {
            $noHp = '62' . substr($noHp, 1);
        }

        // ================= ADMIN =================
        $admin = User::create([
            'nama' => 'Haliq Admin',
            'nim' => null,
            'nip' => '9191919123',
            'username' => 'haliqadmin',
            'password' => Hash::make('1234'),
            'no_hp' => $noHp,
            'foto' => 'default.png',
            'security_question' => 'Apa warna favorit anda?',
            'security_answer' => Hash::make('Biru'),
            'role_id' => 1,
        ]);

        $this->generateQrUser($admin, 'ADM');

        // ================= MAHASISWA =================
        $mhs = User::create([
            'nama' => 'Haliq Mahasiswa',
            'nim' => '136818287',
            'nip' => null,
            'username' => 'haliqmhs',
            'password' => Hash::make('1234'),
            'no_hp' => $noHp,
            'foto' => 'default.png',
            'security_question' => 'Tempat kelahiran anda?',
            'security_answer' => Hash::make('Acehtamiang'),
            'role_id' => 3,
        ]);

        $this->generateQrUser($mhs, 'MHS');

        // ================= DOSEN =================
        $dosen = User::create([
            'nama' => 'Haliq Dosen',
            'nim' => null,
            'nip' => '198512122010011123',
            'username' => 'haliqdosen',
            'password' => Hash::make('1234'),
            'no_hp' => $noHp,
            'foto' => 'default.png',
            'security_question' => 'Anda tinggal?',
            'security_answer' => Hash::make('payameta'),
            'role_id' => 2,
        ]);

        $this->generateQrUser($dosen, 'DSN');
    }

    private function generateQrUser(User $user, string $roleCode): void
    {
        $nomor = str_pad($user->id, 6, '0', STR_PAD_LEFT);
        $qrCode = "USER-{$nomor}-{$roleCode}";

        $qrUrl = 'https://bwipjs-api.metafloor.com/?bcid=qrcode&text='
            . urlencode($qrCode)
            . '&scale=6';

        $user->update([
            'qr_code' => $qrCode,
            'qr_url'  => $qrUrl,
        ]);
    }
}
