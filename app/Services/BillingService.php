<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\BillingCharge;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function createSubscription($user, array $data): Bill
    {
        $periodType = Arr::get($data, 'period_type', 'monthly');
        $startDate = Carbon::parse(Arr::get($data, 'start_date', now()->toDateString()));
        $planAmount = round(Arr::get($data, 'amount', 0), 2);
        $dailyRate = Arr::get($data, 'daily_rate');
        $durationDays = Arr::get($data, 'duration_days');
        $monthlyCycleDay = Arr::get($data, 'monthly_cycle_day', $startDate->day);
        $monthlyOption = Arr::get($data, 'monthly_option', 'same_date');
        $annualDiscountPct = round(Arr::get($data, 'annual_discount_pct', 0), 2);
        $prorata = $monthlyOption === 'prorata';

        $dueDate = $startDate->copy();
        $expiresAt = null;
        $amount = $planAmount;

        switch ($periodType) {
            case 'daily':
                $dailyRate = $dailyRate ? round($dailyRate, 2) : $planAmount;
                $durationDays = max(1, intval($durationDays ?: 1));
                $amount = round($dailyRate * $durationDays, 2);
                $dueDate = $startDate->copy()->addDays($durationDays);
                $expiresAt = $dueDate->copy()->endOfDay();
                break;
            case 'monthly':
                $monthlyCycleDay = min(max(1, intval($monthlyCycleDay)), 28);
                if ($prorata && $startDate->day > 1) {
                    $daysInMonth = $startDate->daysInMonth();
                    $remainingDays = $daysInMonth - $startDate->day + 1;
                    $amount = round(($planAmount / $daysInMonth) * $remainingDays, 2);
                    $dueDate = $startDate->copy()->endOfMonth();
                } else {
                    $amount = round($planAmount, 2);
                    $dueDate = $this->resolveMonthlyDueDate($startDate, $monthlyCycleDay);
                }
                break;
            case 'yearly':
                $annualDiscountPct = min(100, max(0, $annualDiscountPct));
                $amount = round($planAmount * (1 - $annualDiscountPct / 100), 2);
                $dueDate = $startDate->copy()->addYear();
                break;
            default:
                $amount = round($planAmount, 2);
        }

        return $user->bills()->create([
            'name' => Arr::get($data, 'name'),
            'amount' => $amount,
            'plan_amount' => $planAmount,
            'due_date' => $dueDate,
            'frequency' => $periodType,
            'daily_rate' => $dailyRate,
            'duration_days' => $durationDays,
            'monthly_cycle_day' => $monthlyCycleDay,
            'prorata' => $prorata,
            'annual_discount_pct' => $annualDiscountPct,
            'start_date' => $startDate,
            'expires_at' => $expiresAt,
            'status' => 'active',
            'meta' => [
                'monthly_option' => $monthlyOption,
            ],
        ]);
    }

    public function updateSubscription(Bill $bill, array $data): Bill
    {
        $periodType = Arr::get($data, 'period_type', $bill->frequency);
        $startDate = Carbon::parse(Arr::get($data, 'start_date', $bill->start_date ?? now()->toDateString()));
        $planAmount = round(Arr::get($data, 'amount', $bill->plan_amount ?? $bill->amount), 2);
        $dailyRate = Arr::has($data, 'daily_rate') ? Arr::get($data, 'daily_rate') : $bill->daily_rate;
        $durationDays = Arr::has($data, 'duration_days') ? Arr::get($data, 'duration_days') : $bill->duration_days;
        $monthlyCycleDay = Arr::get($data, 'monthly_cycle_day', $bill->monthly_cycle_day ?? $startDate->day);
        $monthlyOption = Arr::get($data, 'monthly_option', $bill->meta['monthly_option'] ?? 'same_date');
        $annualDiscountPct = round(Arr::get($data, 'annual_discount_pct', $bill->annual_discount_pct ?? 0), 2);
        $prorata = $monthlyOption === 'prorata';

        $dueDate = $startDate->copy();
        $expiresAt = null;
        $amount = $planAmount;

        switch ($periodType) {
            case 'daily':
                $dailyRate = $dailyRate ? round($dailyRate, 2) : $planAmount;
                $durationDays = max(1, intval($durationDays ?: 1));
                $amount = round($dailyRate * $durationDays, 2);
                $dueDate = $startDate->copy()->addDays($durationDays);
                $expiresAt = $dueDate->copy()->endOfDay();
                break;
            case 'monthly':
                $monthlyCycleDay = min(max(1, intval($monthlyCycleDay)), 28);
                if ($prorata && $startDate->day > 1) {
                    $daysInMonth = $startDate->daysInMonth();
                    $remainingDays = $daysInMonth - $startDate->day + 1;
                    $amount = round(($planAmount / $daysInMonth) * $remainingDays, 2);
                    $dueDate = $startDate->copy()->endOfMonth();
                } else {
                    $amount = round($planAmount, 2);
                    $dueDate = $this->resolveMonthlyDueDate($startDate, $monthlyCycleDay);
                }
                break;
            case 'yearly':
                $annualDiscountPct = min(100, max(0, $annualDiscountPct));
                $amount = round($planAmount * (1 - $annualDiscountPct / 100), 2);
                $dueDate = $startDate->copy()->addYear();
                break;
            default:
                $amount = round($planAmount, 2);
        }

        $bill->update([
            'name' => Arr::get($data, 'name', $bill->name),
            'amount' => $amount,
            'plan_amount' => $planAmount,
            'due_date' => $dueDate,
            'frequency' => $periodType,
            'daily_rate' => $dailyRate,
            'duration_days' => $durationDays,
            'monthly_cycle_day' => $monthlyCycleDay,
            'prorata' => $prorata,
            'annual_discount_pct' => $annualDiscountPct,
            'start_date' => $startDate,
            'expires_at' => $expiresAt,
            'meta' => ['monthly_option' => $monthlyOption],
            'status' => Arr::get($data, 'status', $bill->status),
        ]);

        return $bill;
    }

    public function processDueBills($user): array
    {
        $notifications = [];
        $today = Carbon::today();

        $bills = $user->bills()->where('status', 'active')->whereDate('due_date', '<=', $today)->orderBy('due_date')->get();

        foreach ($bills as $bill) {
            while ($bill->due_date->lessThanOrEqualTo($today) && $bill->status === 'active') {
                $account = $user->accounts()->orderByDesc('balance')->first();
                if (!$account) {
                    $notifications[] = "Tagihan {$bill->name} belum dapat diproses karena tidak ada akun aktif.";
                    break;
                }

                if ($account->balance < $bill->amount) {
                    $notifications[] = "Saldo tidak cukup untuk membayar tagihan {$bill->name} sebesar Rp " . number_format($bill->amount, 0, ',', '.') . ".";
                    break;
                }

                $category = Category::where('type', 'expense')->first();
                $categoryId = $category ? $category->id : null;

                DB::transaction(function () use ($user, $bill, $account, $categoryId, $today, &$notifications) {
                    $account->applyBalanceChange('expense', $bill->amount);
                    $account->save();

                    $user->transactions()->create([
                        'account_id' => $account->id,
                        'category_id' => $categoryId,
                        'type' => 'expense',
                        'amount' => $bill->amount,
                        'description' => "Pembayaran tagihan {$bill->name}",
                        'date' => $today->toDateString(),
                    ]);

                    BillingCharge::create([
                        'bill_id' => $bill->id,
                        'invoice_date' => $today->toDateString(),
                        'due_date' => $bill->due_date->toDateString(),
                        'amount' => $bill->amount,
                        'discount_pct' => $bill->annual_discount_pct ?? 0,
                        'status' => 'paid',
                        'description' => "Pembayaran otomatis {$bill->name}",
                        'paid_at' => now(),
                    ]);

                    $bill->last_charge_date = $today;

                    switch ($bill->frequency) {
                        case 'daily':
                            $bill->status = 'cancelled';
                            break;
                        case 'monthly':
                            $bill->due_date = $this->resolveMonthlyDueDate($bill->due_date->copy()->addDay(), $bill->monthly_cycle_day ?: $bill->due_date->day);
                            break;
                        case 'yearly':
                            $bill->due_date = $bill->due_date->copy()->addYear();
                            break;
                        default:
                            $bill->due_date = $bill->due_date->copy()->addMonth();
                            break;
                    }

                    $bill->save();
                    $notifications[] = "Tagihan {$bill->name} sebesar Rp " . number_format($bill->amount, 0, ',', '.') . " berhasil dibayarkan.";
                });

                if ($bill->status !== 'active') {
                    break;
                }
            }
        }

        return $notifications;
    }

    protected function resolveMonthlyDueDate(Carbon $date, int $cycleDay): Carbon
    {
        $cycleDay = min(max(1, $cycleDay), 28);
        $candidate = $date->copy();
        $candidate->day = $cycleDay;

        if ($candidate->lessThanOrEqualTo($date)) {
            $candidate->addMonth();
        }

        if ($candidate->day !== $cycleDay) {
            $candidate->day = $cycleDay;
        }

        return $candidate;
    }
}
