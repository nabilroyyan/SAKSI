<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah dulu isi data agar cocok dengan enum baru
        DB::table('absensi')->where('status_surat', 'tertunda')->update(['status_surat' => 'pending']);
        DB::table('absensi')->where('status_surat', 'diterima')->update(['status_surat' => 'approved']);
        DB::table('absensi')->where('status_surat', 'ditolak')->update(['status_surat' => 'rejected']);

        // 2. Baru ubah struktur enum-nya
        Schema::table('absensi', function (Blueprint $table) {
            $table->enum('status_surat', ['pending', 'approved', 'rejected'])
                ->default('approved')
                ->change();
        });
    }

    public function down(): void
    {
        // Kembalikan isi data
        DB::table('absensi')->where('status_surat', 'pending')->update(['status_surat' => 'tertunda']);
        DB::table('absensi')->where('status_surat', 'approved')->update(['status_surat' => 'diterima']);
        DB::table('absensi')->where('status_surat', 'rejected')->update(['status_surat' => 'ditolak']);

        // Kembalikan struktur enum lama
        Schema::table('absensi', function (Blueprint $table) {
            $table->enum('status_surat', ['tertunda', 'diterima', 'ditolak'])
                ->default('diterima')
                ->change();
        });
    }
};
