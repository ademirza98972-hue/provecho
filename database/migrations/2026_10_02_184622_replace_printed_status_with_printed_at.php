<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->timestamp('printed_at')->nullable()->after('disabled_at');
        });

        DB::table('cards')->where('status', 'printed')->update([
            'status' => 'inactive',
            'printed_at' => now(),
        ]);

        DB::statement("ALTER TABLE cards MODIFY status ENUM('inactive','active','disabled') DEFAULT 'inactive'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cards MODIFY status ENUM('inactive','printed','active','disabled') DEFAULT 'inactive'");

        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('printed_at');
        });
    }
};
