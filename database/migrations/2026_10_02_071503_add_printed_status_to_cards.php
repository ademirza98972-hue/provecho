<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE cards MODIFY status ENUM('inactive','printed','active','disabled') DEFAULT 'inactive'");
        DB::statement("ALTER TABLE card_logs MODIFY action ENUM('scan','activated','updated','disabled','reactivated','reset','printed')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cards MODIFY status ENUM('inactive','active','disabled') DEFAULT 'inactive'");
        DB::statement("ALTER TABLE card_logs MODIFY action ENUM('scan','activated','updated','disabled','reactivated','reset')");
    }
};
