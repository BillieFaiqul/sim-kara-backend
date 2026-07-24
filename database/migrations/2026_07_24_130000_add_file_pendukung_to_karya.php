<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karya', function (Blueprint $table) {
            $table->string('file_pendukung_path')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('karya', function (Blueprint $table) {
            $table->dropColumn('file_pendukung_path');
        });
    }
};
