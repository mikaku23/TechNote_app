<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $noHp = '082285926175'; // contoh input
        $noHp = preg_replace('/[^0-9]/', '', $noHp);
        if (substr($noHp, 0, 1) === '0') {
            $noHp = '62' . substr($noHp, 1);
        }


        User::create([
            'nama' => 'Haliq Admin',
            'nim' => null,
            'nip' => '9191919123',
            'username' => 'haliqadmin',
            'password' => '1234',
            'no_hp' => $noHp,
            'foto' => 'default.png',
            'security_question' => 'Apa warna favorit anda?',
            'security_answer' => 'Biru',
            'role_id' => 1,
        ]);

        User::create([
            'nama' => 'Haliq Mahasiswa',
            'nim' => '136818287',
            'nip' => null,
            'username' => 'haliqmhs',
            'password' => '1234',
            'no_hp' => $noHp,
            'foto' => 'default.png',
            'security_question' => 'Tempat kelahiran anda?',
            'security_answer' => 'Acehtamiang',
            'role_id' => 3,
        ]);


        User::create([
            'nama' => 'Haliq Dosen',
            'nim' => null,
            'nip' => '198512122010011123',
            'username' => 'haliqdosen',
            'password' => '1234',
            'no_hp' => $noHp,
            'foto' => 'default.png',
            'security_question' => 'Anda tinggal?',
            'security_answer' => 'Acehtamiang',
            'role_id' => 2,
        ]);
    }
}
