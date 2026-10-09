<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('order_number', 50)->nullable()->after('owner_address')->index();
            $table->string('buyer_name', 100)->nullable()->after('order_number');
            $table->string('buyer_phone', 20)->nullable()->after('buyer_name');
        });
    }

    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn(['order_number', 'buyer_name', 'buyer_phone']);
        });
    }
};
