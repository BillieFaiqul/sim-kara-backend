<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karya', function (Blueprint $table) {
            $table->string('nama')->nullable()->after('judul')->comment('Nama penulis/pembuat karya');
            $table->string('nip_nim')->nullable()->after('nama')->comment('NIP/NIM penulis/pembuat karya');
        });
    }

    public function down(): void
    {
        Schema::table('karya', function (Blueprint $table) {
            $table->dropColumn(['nama', 'nip_nim']);
        });
    }
};
