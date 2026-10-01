<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->enum('status', ['inactive', 'active', 'disabled'])->default('inactive');
            $table->text('google_url')->nullable();
            $table->string('place_id', 255)->nullable();
            $table->string('owner_name', 255)->nullable();
            $table->text('owner_address')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
