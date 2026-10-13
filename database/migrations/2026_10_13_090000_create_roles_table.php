<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->json('permissions');
            $table->timestamps();
        });

        // users.role tetap: 'admin' = admin utama (akses penuh), 'reseller' = user biasa yang aksesnya dari role_id.
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->restrictOnDelete();
        });

        // Reseller lama masuk role "Reseller" tanpa hak tambahan: akses persis seperti sebelumnya.
        $id = DB::table('roles')->insertGetId(['name' => 'Reseller', 'permissions' => '[]', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('users')->where('role', 'reseller')->update(['role_id' => $id]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
        Schema::dropIfExists('roles');
    }
};
