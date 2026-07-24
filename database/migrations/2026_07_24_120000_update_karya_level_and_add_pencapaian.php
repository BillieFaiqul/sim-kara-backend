<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karya', function (Blueprint $table) {
            // Drop the old level column and recreate as string
            $table->dropColumn('level');
        });

        Schema::table('karya', function (Blueprint $table) {
            $table->string('level')->after('jenis')->default('Local');
            $table->string('pencapaian')->nullable()->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('karya', function (Blueprint $table) {
            $table->dropColumn('pencapaian');
            $table->dropColumn('level');
        });

        Schema::table('karya', function (Blueprint $table) {
            $table->enum('level', ['Lokal', 'Nasional', 'Internasional'])->default('Lokal')->after('jenis');
        });
    }
};
