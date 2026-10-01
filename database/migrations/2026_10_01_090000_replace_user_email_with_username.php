<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->after('name')->default('');
        });

        DB::table('users')->update(['username' => DB::raw("SUBSTRING_INDEX(email, '@', 1)")]);

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
            $table->dropColumn(['email', 'email_verified_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
