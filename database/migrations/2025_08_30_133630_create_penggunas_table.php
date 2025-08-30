<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penggunas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 30);
            $table->string('username', 50);
            $table->string('password', 100);
            $table->string('no_hp', 13);
            $table->string('email', 100);
            $table->enum('jenis_pengguna', ['mahasiswa', 'dosen', 'staff', 'Unit']);
            $table->string('fakultas', 30);
            $table->string('prodi', 30);
            $table->date('tanggal_daftar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunas');
    }
};
