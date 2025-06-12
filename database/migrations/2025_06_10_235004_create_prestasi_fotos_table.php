<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prestasi_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestasi_id')->constrained('prestasi')->onDelete('cascade');
            $table->string('file_path'); // Untuk menyimpan path ke file foto
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('prestasi_fotos');
    }
};