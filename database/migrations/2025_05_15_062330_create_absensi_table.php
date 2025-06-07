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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa']);
            $table->string('foto_surat')->nullable();
            $table->date('hari_tanggal');
            $table->enum('status_surat', ['tertunda', 'diterima', 'ditolak']);
            $table->string('catatan')->nullable();
            $table->foreignId('id_siswa')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kelas_siswa_id')->constrained('kelas_siswa')->onDelete('cascade');
            $table->foreignId('id_users')->constrained('users')->onDelete('cascade'); // petugas
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
