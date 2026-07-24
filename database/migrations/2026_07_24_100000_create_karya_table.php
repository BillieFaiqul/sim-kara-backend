<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->enum('jenis', ['Publikasi', 'Penelitian', 'Pengabdian', 'Prestasi', 'HKI', 'Artikel'])->default('Publikasi');
            $table->enum('level', ['Lokal', 'Nasional', 'Internasional'])->default('Lokal');
            $table->year('tahun');
            $table->text('deskripsi')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified', 'rejected'])->default('draft');
            $table->text('alasan_reject')->nullable();
            $table->date('tanggal_submit')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karya');
    }
};
