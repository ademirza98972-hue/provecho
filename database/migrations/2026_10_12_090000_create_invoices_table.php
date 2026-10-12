<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('buyer_name', 100);
            $table->string('buyer_phone', 20)->nullable();
            $table->string('payment_method', 20);
            $table->unsignedInteger('discount')->default(0);
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });

        // Harga disalin per baris: nota lama tidak berubah walau card nanti direset dan dijual lagi.
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('card_id', 20)->index();
            $table->unsignedInteger('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
