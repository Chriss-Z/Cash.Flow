<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;    
use App\Models\Transaction;
use App\Models\Target;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $categories = Category::all();

        // Total saldo
        $totalSaldo = Transaction::where('user_id', $userId)
            ->selectRaw("
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) -
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END)
                as saldo
            ")
            ->value('saldo') ?? 0;

        $accounts = collect([
        [
            'type' => 'Debit Card',
            'title' => 'Paspor blue debit',
            'number' => '**** **** **** 2598',
            'formatted' => 'Rp ' . number_format($totalSaldo, 0, ',', '.'),
            'currency_icon' => '💳',
            'chipStyle' => ''
        ]
    ]);
  $breakdowns = Transaction::where('transactions.user_id', $userId)
    ->where('transactions.type', 'expense') // tambahkan prefix
    ->join('categories', 'transactions.category_id', '=', 'categories.id')
    ->selectRaw('categories.name as category, SUM(transactions.amount) as amount, transactions.category_id')
    ->groupBy('categories.name', 'transactions.category_id')
    ->get()
    ->map(function ($item) use ($userId) {
        // total bulan ini
        $currentMonth = Transaction::where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.date', now()->month)
            ->where('transactions.category_id', $item->category_id)
            ->sum('transactions.amount');

        // total bulan lalu
        $lastMonth = Transaction::where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.date', now()->subMonth()->month)
            ->where('transactions.category_id', $item->category_id)
            ->sum('transactions.amount');

        $change = $currentMonth - $lastMonth;
        $direction = $change < 0 ? 'down' : 'up';

        return [
            'label' => $item->category ?? 'Lainnya',
            'amount' => $currentMonth,
            'change' => abs($change),
            'change_direction' => $direction,
        ];
    })
    ->toArray();





        // Target
        $target = Target::where('user_id', $userId)->latest()->first();

        $progress = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->sum('amount');

        $percentage = $target && $target->target_amount > 0
            ? ($progress / $target->target_amount) * 100
            : 0;

        // Transaksi terbaru
        $transactions = Transaction::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Statistik chart ready untuk periode mingguan, bulanan, dan tahunan
        $today = now()->startOfDay();

        $weekly = [];
        $previousWeekly = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $weekly[] = [
                'label' => $date->format('D'),
                'amount' => Transaction::where('user_id', $userId)
                    ->whereDate('date', $date)
                    ->where('type', 'expense')
                    ->sum('amount'),
            ];

            $previousWeekly[] = Transaction::where('user_id', $userId)
                ->whereDate('date', $today->copy()->subDays($i + 7))
                ->where('type', 'expense')
                ->sum('amount');
        }

        $monthly = [];
        $previousMonthly = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $today->copy()->subMonths($i);
            $monthly[] = [
                'label' => $date->format('M'),
                'amount' => Transaction::where('user_id', $userId)
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->where('type', 'expense')
                    ->sum('amount'),
            ];

            $previousMonthly[] = Transaction::where('user_id', $userId)
                ->whereMonth('date', $date->copy()->subYear()->month)
                ->whereYear('date', $date->copy()->subYear()->year)
                ->where('type', 'expense')
                ->sum('amount');
        }

        $yearly = [];
        $previousYearly = [];
        for ($i = 4; $i >= 0; $i--) {
            $date = $today->copy()->subYears($i);
            $yearly[] = [
                'label' => $date->format('Y'),
                'amount' => Transaction::where('user_id', $userId)
                    ->whereYear('date', $date->year)
                    ->where('type', 'expense')
                    ->sum('amount'),
            ];

            $previousYearly[] = Transaction::where('user_id', $userId)
                ->whereYear('date', $date->copy()->subYear()->year)
                ->where('type', 'expense')
                ->sum('amount');
        }

        $chartData = [
            'weekly' => [
                'title' => 'Perbandingan Mingguan',
                'subtitle' => $today->copy()->subDays(6)->format('d M') . ' - ' . $today->format('d M'),
                'items' => $weekly,
                'total' => array_sum(array_column($weekly, 'amount')), 
                'previous_total' => array_sum($previousWeekly),
            ],
            'monthly' => [
                'title' => 'Perbandingan Bulanan',
                'subtitle' => $today->copy()->subMonths(5)->format('M') . ' - ' . $today->format('M'),
                'items' => $monthly,
                'total' => array_sum(array_column($monthly, 'amount')),
                'previous_total' => array_sum($previousMonthly),
            ],
            'yearly' => [
                'title' => 'Perbandingan Tahunan',
                'subtitle' => $today->copy()->subYears(4)->format('Y') . ' - ' . $today->format('Y'),
                'items' => $yearly,
                'total' => array_sum(array_column($yearly, 'amount')),
                'previous_total' => array_sum($previousYearly),
            ],
        ];

        return view('beranda', compact(
            'totalSaldo',
            'target',
            'progress',
            'percentage',
            'transactions',
            'accounts', 
            'breakdowns',
            'categories',
            'bills',
            'notifications',
            'chartData'
        ));
    }
}
