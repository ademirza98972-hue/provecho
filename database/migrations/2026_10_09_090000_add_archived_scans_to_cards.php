<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            // Jumlah scan yang detailnya sudah dibersihkan (lihat perintah scans:prune).
            $table->unsignedInteger('archived_scans')->default(0)->after('printed_at');
        });
    }

    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('archived_scans');
        });
    }
};
