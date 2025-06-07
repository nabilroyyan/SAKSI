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
       Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->onDelete('cascade');
            $table->foreignId('kelas_siswa_id')->constrained('kelas_siswa')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('no_telepon');
            $table->string('alasan');
            $table->string('nomer_wali');
            $table->enum('status_validasi', ['pending', 'diterima', 'ditolak', 'berhenti'])->default('pending');
            $table->foreignId('validator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('catatan_pembina')->nullable();
            $table->timestamp('tanggal_pendaftaran')->nullable();
            $table->timestamp('tanggal_validasi')->nullable();   
            $table->timestamp('tanggal_berhenti')->nullable();
            $table->timestamps();
        });       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
