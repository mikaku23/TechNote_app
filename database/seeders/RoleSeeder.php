<?php

namespace Database\Seeders;

use App\Models\role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        role::insert([
            ['status' => 'Admin'],
            ['status' => 'Dosen'],
            ['status' => 'Mahasiswa'],
            ['status' => 'Teknisi'],
        ]);
    }
}
