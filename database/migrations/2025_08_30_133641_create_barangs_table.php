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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 30);
            $table->string('kategori', 30)->nullable();
            $table->string('merek', 30)->nullable();
            $table->string('model', 30)->nullable();
            $table->string('serial_number', 30)->nullable();
            $table->string('lokasi_barang', 30);
            $table->enum('kepemilikan', ['pribadi', 'universitas']);
            $table->foreignId('id_pengguna')->references('id')->on('penggunas')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
