<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            if (!Schema::hasColumn('bills', 'plan_amount')) {
                $table->decimal('plan_amount', 15, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('bills', 'daily_rate')) {
                $table->decimal('daily_rate', 15, 2)->nullable()->after('plan_amount');
            }
            if (!Schema::hasColumn('bills', 'duration_days')) {
                $table->integer('duration_days')->unsigned()->nullable()->after('daily_rate');
            }
            if (!Schema::hasColumn('bills', 'monthly_cycle_day')) {
                $table->tinyInteger('monthly_cycle_day')->unsigned()->nullable()->after('duration_days');
            }
            if (!Schema::hasColumn('bills', 'prorata')) {
                $table->boolean('prorata')->default(false)->after('monthly_cycle_day');
            }
            if (!Schema::hasColumn('bills', 'annual_discount_pct')) {
                $table->decimal('annual_discount_pct', 5, 2)->default(0)->after('prorata');
            }
            if (!Schema::hasColumn('bills', 'start_date')) {
                $table->date('start_date')->nullable()->after('annual_discount_pct');
            }
            if (!Schema::hasColumn('bills', 'expires_at')) {
                $table->dateTime('expires_at')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('bills', 'status')) {
                $table->enum('status', ['active', 'paused', 'cancelled'])->default('active')->after('expires_at');
            }
            if (!Schema::hasColumn('bills', 'meta')) {
                $table->json('meta')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn([
                'plan_amount',
                'daily_rate',
                'duration_days',
                'monthly_cycle_day',
                'prorata',
                'annual_discount_pct',
                'start_date',
                'expires_at',
                'status',
                'meta',
            ]);
        });
    }
};
