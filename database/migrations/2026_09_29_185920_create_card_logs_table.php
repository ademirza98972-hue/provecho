<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_logs', function (Blueprint $table) {
            $table->id();
            $table->string('card_id', 20);
            $table->enum('action', ['scan', 'activated', 'updated', 'disabled']);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_logs');
    }
};
