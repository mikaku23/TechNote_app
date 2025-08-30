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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->text('keluhan');
            $table->enum('status', ['menunggu', 'proses', 'selesai', 'diambil', 'batal']);
            $table->date('tanggal_masuk');
            $table->date('tanggal_selesai');
            $table->text('tindakan');
            $table->string('biaya', 30);
            $table->text('catatan')->nullable();
            $table->foreignId('id_barang')->references('id')->on('barangs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_teknisi')->references('id')->on('teknisis')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
