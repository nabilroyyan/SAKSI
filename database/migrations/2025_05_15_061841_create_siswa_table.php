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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nis_nip')->unique();
            $table->string('nama_siswa');
            $table->string('email');
            $table->string('tempat');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->enum('agama', ['islam', 'protestan', 'katolik', 'hindu', 'budha','khonghucu']);
            $table->string('no_telepon');
            $table->string('tahun_masuk');
            $table->enum('status', ['aktif','tidak_aktif'])->default('aktif');
            $table->string('kode')->nullable();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
