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
        User::create([
            'nama' => 'Haliq Admin',
            'nim' => null,
            'nip' => '9191919123',
            'username' => 'haliqadmin',
            'password' => '1234',
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
            'foto' => 'default.png',
            'security_question' => 'Anda tinggal?',
            'security_answer' => 'Acehtamiang',
            'role_id' => 2,
        ]);
    }
}
