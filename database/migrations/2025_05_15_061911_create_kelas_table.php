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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->foreignId('id_jurusan')->constrained('jurusan')->onDelete('cascade');
            $table->foreignId('id_users')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('stt', ['tidak_aktif', 'aktif']); // wali kelas
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
