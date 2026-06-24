<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bills')) {
            return;
        }

        try {
            DB::statement("ALTER TABLE bills MODIFY frequency ENUM('daily','weekly','monthly','quarterly','yearly') NOT NULL");
        } catch (\Throwable $e) {
            try {
                Schema::table('bills', function (Blueprint $table) {
                    $table->string('frequency')->change();
                });
            } catch (\Throwable $inner) {
                // fallback, do not block migration if driver does not support enum change.
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bills')) {
            return;
        }

        try {
            DB::statement("ALTER TABLE bills MODIFY frequency ENUM('monthly','yearly','weekly','quarterly') NOT NULL");
        } catch (\Throwable $e) {
            // rollback best effort
        }
    }
};
