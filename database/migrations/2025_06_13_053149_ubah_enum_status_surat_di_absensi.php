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
        Schema::table('absensi', function (Blueprint $table) {
            // Ubah enum lama ke enum baru
            $table->enum('status_surat', ['pending', 'approved', 'rejected'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            // Kembalikan enum ke versi lama
            $table->enum('status_surat', ['tertunda', 'diterima', 'ditolak'])->default('diterima')->change();
        });
    }
};
