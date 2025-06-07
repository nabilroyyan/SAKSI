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
        Schema::create('sesi_absen_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal_ekstrakurikuler')->onDelete('cascade');
            $table->foreignId('guru_pembina_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('waktu_buka');
            $table->timestamp('waktu_tutup')->nullable();
            $table->boolean('is_active')->default(true);
             $table->boolean('absensi_telah_disimpan')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_absen_ekstrakurikuler');
    }
};
