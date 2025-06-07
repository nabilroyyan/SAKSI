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
        Schema::create('absensi_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_absen_ekstrakurikuler_id')->constrained('sesi_absen_ekstrakurikuler')->onDelete('cascade');
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->onDelete('cascade');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_ekstrakurikuler');
    }
};
