<?php

namespace App\Services;

use App\Models\Target;
use Carbon\Carbon;

class NotificationService
{
    public function __construct(
        protected BillingService $billingService,
    ) {
    }

    public function forUser($user, int $limit = 8): array
    {
        $notifications = [];

        $notifications = array_merge($notifications, $this->billingService->processDueBills($user));

        $recentTransactions = $user->transactions()
            ->with('category')
            ->where('created_at', '>=', Carbon::now()->subDays(3))
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentTransactions as $transaction) {
            $typeLabel = $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
            $description = trim((string) $transaction->description);
            $categoryName = $transaction->category?->name;
            $detail = $categoryName ? trim($description . ' (' . $categoryName . ')') : $description;

            if ($detail === '') {
                $detail = $categoryName ?? 'Transaksi terbaru';
            }

            $notifications[] = sprintf(
                '%s %s sebesar Rp %s dicatat.',
                $typeLabel,
                $detail,
                number_format((float) $transaction->amount, 0, ',', '.')
            );
        }

        $target = Target::where('user_id', $user->id)->latest()->first();
        if ($target && (float) $target->target_amount > 0) {
            $progressPercent = min(100, round(((float) $target->current_amount / (float) $target->target_amount) * 100));

            if ($progressPercent >= 100) {
                $notifications[] = 'Target bulanan sudah tercapai.';
            } elseif ($progressPercent >= 75) {
                $notifications[] = 'Target bulanan sudah ' . $progressPercent . '% tercapai.';
            }
        }

        $totalBalance = (float) $user->accounts()->sum('balance');
        if ($totalBalance > 0 && $totalBalance < 100000) {
            $notifications[] = 'Saldo total menipis: Rp ' . number_format($totalBalance, 0, ',', '.') . '.';
        }

        $notifications = array_values(array_filter(array_unique($notifications)));

        return array_slice($notifications, 0, $limit);
    }
}