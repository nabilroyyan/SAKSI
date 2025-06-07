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
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ekstrakurikuler');
            $table->string('gambar')->nullable();
            $table->text('deskripsi');
            $table->foreignId('id_kategori')->constrained('kategori_ekstra')->onDelete('cascade');
            $table->string('lokasi');
            $table->foreignId('id_users')->constrained('users')->onDelete('cascade'); // guru pembimbing
            $table->enum('periode', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->enum('jenis', ['wajib', 'pilihan'])->default('wajib');
            $table->integer('kuota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler');
    }
};
