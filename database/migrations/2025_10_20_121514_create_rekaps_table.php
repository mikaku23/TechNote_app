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
        Schema::create('rekaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perbaikan_id')->nullable()->references('id')->on('perbaikans')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('penginstalan_id')->nullable()->references('id')->on('penginstalans')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['dihapus', 'tersedia']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekaps');
    }
};
