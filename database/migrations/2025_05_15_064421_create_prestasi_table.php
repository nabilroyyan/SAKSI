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
        Schema::create('prestasi', function (Blueprint $table) {
            $table->id();
            // Untuk siswa aktif
            $table->foreignId('pendaftaran_id')->nullable()->constrained('pendaftaran')->onDelete('cascade');
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->onDelete('cascade');
            $table->foreignId('ekstrakurikuler_id')->nullable()->constrained('ekstrakurikuler')->onDelete('set null');
            $table->string('nama_kegiatan', 100);
            $table->string('peringkat', 50);
            $table->date('tanggal_kejuaraan');
            $table->string('tingkat_kejuaraan', 50);
            $table->text('deskripsi')->nullable();
            $table->string('foto_prestasi', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi');
    }
};
