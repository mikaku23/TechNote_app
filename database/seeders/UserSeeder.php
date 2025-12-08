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
            'nama' => 'Muhammad Haliq Maulana',
            'nim' => null,
            'nip' => '91919191', // bisa null jika tidak dipakai
            'username' => 'haliq',
            'password' => '1234', // otomatis di-hash oleh cast model
            'foto' => 'default.png',
            'security_question' => 'Apa warna favorit anda?',
            'security_answer' => 'Biru',
            'role_id' => 1, // pastikan role admin id-nya 1
        ]);
    }
}
