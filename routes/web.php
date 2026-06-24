<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Bill;
use App\Models\Target;
use App\Services\BillingService;
use Carbon\Carbon;



Route::view('/login', 'Auth.login')->name('login');
Route::view('/register', 'Auth.register')->name('register');
Route::view('/forgot-password', 'Auth.forget')->name('password.request');
Route::get('/beranda', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $billingService = new BillingService();

    if ($user->accounts()->count() === 0) {
        $user->accounts()->createMany([
            [
                'name' => 'Paspor blue debit',
                'type' => 'debit',
                'account_number' => '**** **** **** 2598',
                'balance' => 0,
            ],
            [
                'name' => 'Rupiah',
                'type' => 'cash',
                'account_number' => null,
                'balance' => 0,
            ],
        ]);
    }

    $accounts = $user->accounts()->get()->map(function ($account) {
        return [
            'type' => $account->type === 'cash' ? 'Uang Tunai' : 'Debit Card',
            'title' => $account->type === 'cash' ? 'Rupiah' : $account->name,
            'number' => $account->account_number,
            'amount' => $account->balance,
            'formatted' => 'Rp ' . number_format($account->balance, 0, ',', '.'),
            'currency_icon' => $account->type === 'cash' ? '💵' : '',
            'chipStyle' => $account->type === 'cash' ? 'cash-icon' : 'brand-badge',
        ];
    });

    $totalSaldo = $accounts->sum('amount');
    $target = Target::where('user_id', $user->id)->latest()->first();
    $progress = $target ? $target->current_amount : 0;
    $percentage = $target && $target->target_amount > 0
        ? ($progress / $target->target_amount) * 100
        : 0;
    $transactions = $user->transactions()->with('category')->latest()->take(5)->get();
    $notifications = $billingService->processDueBills($user);
    $bills = $user->bills()->orderBy('due_date')->get()->map(function ($bill) {
        return [
            'title' => $bill->name,
            'date' => $bill->due_date->format('d M Y'),
            'last_charge' => $bill->last_charge_date ? $bill->last_charge_date->format('d M Y') : 'Belum dibayar',
            'amount' => $bill->amount,
            'frequency' => ucfirst($bill->frequency),
        ];
    });
    $breakdowns = $user->transactions()
        ->with('category')
        ->where('type', 'expense')
        ->get()
        ->groupBy(function ($trx) {
            return $trx->category->name ?? 'Lainnya';
        })
        ->map(function ($group, $name) {
            return [
                'label' => $name,
                'amount' => $group->sum('amount'),
            ];
        });

    $today = now()->startOfDay();
    $weekly = [];
    $previousWeekly = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = $today->copy()->subDays($i);
        $currentAmount = Transaction::where('user_id', $user->id)
            ->whereDate('date', $date->toDateString())
            ->where('type', 'expense')
            ->sum('amount');
        $previousAmount = Transaction::where('user_id', $user->id)
            ->whereDate('date', $today->copy()->subDays($i + 7)->toDateString())
            ->where('type', 'expense')
            ->sum('amount');

        if ($currentAmount > 0 || $previousAmount > 0) {
            $weekly[] = [
                'label' => $date->format('D'),
                'amount' => $currentAmount,
                'previous_amount' => $previousAmount,
            ];
            $previousWeekly[] = $previousAmount;
        }
    }

    $monthly = [];
    $previousMonthly = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = $today->copy()->subMonths($i);
        $currentAmount = Transaction::where('user_id', $user->id)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('type', 'expense')
            ->sum('amount');
        $previousAmount = Transaction::where('user_id', $user->id)
            ->whereMonth('date', $date->copy()->subYear()->month)
            ->whereYear('date', $date->copy()->subYear()->year)
            ->where('type', 'expense')
            ->sum('amount');

        if ($currentAmount > 0 || $previousAmount > 0) {
            $monthly[] = [
                'label' => $date->format('M'),
                'amount' => $currentAmount,
                'previous_amount' => $previousAmount,
            ];
            $previousMonthly[] = $previousAmount;
        }
    }

    $yearly = [];
    $previousYearly = [];
    for ($i = 4; $i >= 0; $i--) {
        $date = $today->copy()->subYears($i);
        $currentAmount = Transaction::where('user_id', $user->id)
            ->whereYear('date', $date->year)
            ->where('type', 'expense')
            ->sum('amount');
        $previousAmount = Transaction::where('user_id', $user->id)
            ->whereYear('date', $date->copy()->subYear()->year)
            ->where('type', 'expense')
            ->sum('amount');

        if ($currentAmount > 0 || $previousAmount > 0) {
            $yearly[] = [
                'label' => $date->format('Y'),
                'amount' => $currentAmount,
                'previous_amount' => $previousAmount,
            ];
            $previousYearly[] = $previousAmount;
        }
    }

    $chartData = [
        'weekly' => [
            'title' => 'Perbandingan Mingguan',
            'subtitle' => count($weekly) ? $weekly[0]['label'] . ' - ' . end($weekly)['label'] : 'Tidak ada data',
            'items' => $weekly,
            'total' => array_sum(array_column($weekly, 'amount')),
            'previous_total' => array_sum($previousWeekly),
        ],
        'monthly' => [
            'title' => 'Perbandingan Bulanan',
            'subtitle' => count($monthly) ? $monthly[0]['label'] . ' - ' . end($monthly)['label'] : 'Tidak ada data',
            'items' => $monthly,
            'total' => array_sum(array_column($monthly, 'amount')),
            'previous_total' => array_sum($previousMonthly),
        ],
        'yearly' => [
            'title' => 'Perbandingan Tahunan',
            'subtitle' => count($yearly) ? $yearly[0]['label'] . ' - ' . end($yearly)['label'] : 'Tidak ada data',
            'items' => $yearly,
            'total' => array_sum(array_column($yearly, 'amount')),
            'previous_total' => array_sum($previousYearly),
        ],
    ];

    return view('beranda', compact('totalSaldo', 'accounts', 'target', 'progress', 'percentage', 'transactions', 'bills', 'breakdowns', 'weekly', 'notifications', 'chartData'));
})->name('beranda');

Route::get('/target', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $target = Target::where('user_id', $user->id)->latest()->first();
    $target = $target ?: new Target(['target_amount' => 0, 'current_amount' => 0]);
    $progress = $target->current_amount;
    $percentage = $target->target_amount > 0
        ? min(100, ($progress / $target->target_amount) * 100)
        : 0;

    return view('target', compact('target', 'progress', 'percentage'));
})->name('target');

Route::post('/target', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $data = $request->validate([
        'target_amount' => 'required|numeric|min:0',
        'current_amount' => 'required|numeric|min:0',
    ]);

    $user = Auth::user();
    Target::updateOrCreate([
        'user_id' => $user->id,
        'month' => now()->month,
        'year' => now()->year,
    ], [
        'target_amount' => $data['target_amount'],
        'current_amount' => $data['current_amount'],
    ]);

    return redirect()->route('target')->with('status', 'Target berhasil diperbarui.');
})->name('target.save');

Route::get('/saldo', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $billingService = new BillingService();
    $notifications = $billingService->processDueBills($user);
    $accounts = $user->accounts()->get();
    $bills = $user->bills()->where('status', 'active')->orderBy('due_date')->take(3)->get();

    return view('saldo', compact('accounts', 'bills', 'notifications'));
})->name('saldo');

Route::get('/tagihan', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $billingService = new BillingService();
    $notifications = $billingService->processDueBills($user);
    $bills = $user->bills()->orderBy('due_date')->get();

    return view('tagihan', compact('bills', 'notifications'));
})->name('tagihan');

Route::get('/pengeluaran', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $today = now();

    $weekly = [];
    $monthly = [];
    $yearly = [];

    // Weekly: last 7 days
    for ($i = 6; $i >= 0; $i--) {
        $date = $today->copy()->subDays($i);
        $current = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereDate('date', $date->toDateString())
            ->sum('amount');
        $previous = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereDate('date', $date->copy()->subWeek()->toDateString())
            ->sum('amount');

        if ($current > 0 || $previous > 0) {
            $weekly[] = [
                'label' => $date->format('D'),
                'current' => $current,
                'previous' => $previous,
            ];
        }
    }

    // Monthly: last 6 months vs same months previous year
    for ($i = 5; $i >= 0; $i--) {
        $date = $today->copy()->subMonths($i);
        $current = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->sum('amount');
        $previous = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->copy()->subYear()->year)
            ->sum('amount');

        if ($current > 0 || $previous > 0) {
            $monthly[] = [
                'label' => $date->format('M'),
                'current' => $current,
                'previous' => $previous,
            ];
        }
    }

    // Yearly: current year vs previous year
    $currentYear = $today->year;
    $previousYear = $today->copy()->subYear()->year;

    $currentYearAmount = Transaction::where('user_id', $user->id)
        ->where('type', 'expense')
        ->whereYear('date', $currentYear)
        ->sum('amount');
    $previousYearAmount = Transaction::where('user_id', $user->id)
        ->where('type', 'expense')
        ->whereYear('date', $previousYear)
        ->sum('amount');

    if ($currentYearAmount > 0 || $previousYearAmount > 0) {
        $yearly[] = ['label' => $previousYear, 'current' => $previousYearAmount, 'previous' => 0];
        $yearly[] = ['label' => $currentYear, 'current' => $currentYearAmount, 'previous' => 0];
    }

    $chartData = [
    'weekly' => [
        'title' => 'Perbandingan Mingguan',
        'subtitle' => count($weekly)
            ? $weekly[0]['label'].' - '.end($weekly)['label']
            : 'Tidak ada data mingguan',
        'items' => $weekly,
        'current_label' => 'Minggu Ini',
        'previous_label' => 'Minggu Lalu',
    ],

    'monthly' => [
        'title' => 'Perbandingan Bulanan',
        'subtitle' => count($monthly)
            ? $monthly[0]['label'].' - '.end($monthly)['label']
            : 'Tidak ada data bulanan',
        'items' => $monthly,
        'current_label' => 'Bulan Ini',
        'previous_label' => 'Bulan Lalu',
    ],

    'yearly' => [
        'title' => 'Perbandingan Tahunan',
        'subtitle' => $previousYear.' vs '.$currentYear,
        'items' => $yearly,
        'current_label' => (string)$currentYear,
        'previous_label' => (string)$previousYear,
    ],
];

$categoryCards = Transaction::where('user_id', $user->id)
    ->where('type', 'expense')
    ->with('category')
    ->get()
    ->groupBy('category_id')
    ->map(function ($items) {
        return [
            'label' => optional($items->first()->category)->name ?? 'Lainnya',
            'amount' => $items->sum('amount'),
            'count' => $items->count(),
        ];
    })
    ->values()
    ->toArray();
    

    return view('pengeluaran', compact('chartData', 'categoryCards'));
})->name('pengeluaran');

Route::post('/tagihan/add', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'period_type' => 'required|in:daily,monthly,yearly',
        'start_date' => 'required|date',
        'monthly_option' => 'nullable|in:same_date,prorata',
        'monthly_cycle_day' => 'nullable|integer|min:1|max:28',
        'daily_rate' => 'nullable|numeric|min:0',
        'duration_days' => 'nullable|integer|min:1',
        'annual_discount_pct' => 'nullable|numeric|min:0|max:100',
    ]);

    $billingService = new BillingService();
    $billingService->createSubscription(Auth::user(), $request->only([
        'name',
        'amount',
        'period_type',
        'start_date',
        'monthly_option',
        'monthly_cycle_day',
        'daily_rate',
        'duration_days',
        'annual_discount_pct',
    ]));

    return redirect()->route('tagihan')->with('status', 'Tagihan berhasil ditambahkan.');
})->middleware('auth')->name('tagihan.add');

Route::post('/tagihan/update', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'bill_id' => 'required|integer',
        'name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'period_type' => 'required|in:daily,monthly,yearly',
        'start_date' => 'required|date',
        'monthly_option' => 'nullable|in:same_date,prorata',
        'monthly_cycle_day' => 'nullable|integer|min:1|max:28',
        'daily_rate' => 'nullable|numeric|min:0',
        'duration_days' => 'nullable|integer|min:1',
        'annual_discount_pct' => 'nullable|numeric|min:0|max:100',
    ]);

    $bill = Auth::user()->bills()->find($request->bill_id);
    if (!$bill) {
        return redirect()->route('tagihan')->with('error', 'Tagihan tidak ditemukan.');
    }

    $billingService = new BillingService();
    $billingService->updateSubscription($bill, $request->only([
        'name',
        'amount',
        'period_type',
        'start_date',
        'monthly_option',
        'monthly_cycle_day',
        'daily_rate',
        'duration_days',
        'annual_discount_pct',
    ]));

    return redirect()->route('tagihan')->with('status', 'Tagihan berhasil diperbarui.');
})->middleware('auth')->name('tagihan.update');

Route::post('/tagihan/delete', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'bill_id' => 'required|integer',
    ]);

    $bill = Auth::user()->bills()->find($request->bill_id);
    if (!$bill) {
        return redirect()->route('tagihan')->with('error', 'Tagihan tidak ditemukan.');
    }

    $bill->delete();
    return redirect()->route('tagihan')->with('status', 'Tagihan berhasil dihapus.');
})->middleware('auth')->name('tagihan.delete');

Route::get('/transaksi', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $billingService = new BillingService();
    $notifications = $billingService->processDueBills($user);
    
    if ($user->accounts()->count() === 0) {
        $user->accounts()->createMany([
            [
                'name' => 'Paspor blue debit',
                'type' => 'debit',
                'account_number' => '**** **** **** 2598',
                'balance' => 0,
            ],
            [
                'name' => 'Rupiah',
                'type' => 'cash',
                'account_number' => null,
                'balance' => 0,
            ],
        ]);
    }

    $accounts = $user->accounts()->get();
    $categories = Category::orderBy('type')->orderBy('name')->get();
    $transactions = $user->transactions()->with(['category', 'account'])->latest()->get();
    $incomeTotal = $transactions->where('type', 'income')->sum('amount');
    $expenseTotal = $transactions->where('type', 'expense')->sum('amount');

    return view('transaksi', compact('accounts', 'categories', 'transactions', 'incomeTotal', 'expenseTotal', 'notifications'));
})->name('transaksi');

Route::post('/transaksi/add', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'account_id' => 'required|integer',
        'category_id' => 'required|integer',
        'type' => 'required|in:income,expense',
        'amount' => 'required|numeric|min:0',
        'description' => 'required|string|max:255',
        'date' => 'required|date',
    ]);

    $user = Auth::user();
    $account = $user->accounts()->find($request->account_id);
    $category = Category::find($request->category_id);

    if (!$account) {
        return back()->with('error', 'Akun tidak ditemukan.');
    }
    if (!$category || $category->type !== $request->type) {
        return back()->with('error', 'Kategori tidak ditemukan atau jenis kategori tidak sesuai.');
    }

    $transaction = $user->transactions()->create([
        'account_id' => $account->id,
        'category_id' => $category->id,
        'type' => $request->type,
        'amount' => $request->amount,
        'description' => $request->description,
        'date' => $request->date,
    ]);

    $account->balance += $request->type === 'income' ? $request->amount : -$request->amount;
    $account->save();

    return back()->with('status', 'Transaksi berhasil ditambahkan.');
})->middleware('auth')->name('transaksi.add');

Route::post('/transaksi/update', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'transaction_id' => 'required|integer',
        'account_id' => 'required|integer',
        'category_id' => 'required|integer',
        'type' => 'required|in:income,expense',
        'amount' => 'required|numeric|min:0',
        'description' => 'required|string|max:255',
        'date' => 'required|date',
    ]);

    $user = Auth::user();
    $transaction = $user->transactions()->find($request->transaction_id);

    if (!$transaction) {
        return back()->with('error', 'Transaksi tidak ditemukan.');
    }

    $oldAccount = $transaction->account;
    $newAccount = $user->accounts()->find($request->account_id);
    $category = Category::find($request->category_id);

    if (!$newAccount) {
        return back()->with('error', 'Akun tidak ditemukan.');
    }
    if (!$category || $category->type !== $request->type) {
        return back()->with('error', 'Kategori tidak ditemukan atau jenis kategori tidak sesuai.');
    }

    $oldSign = $transaction->type === 'income' ? 1 : -1;
    $newSign = $request->type === 'income' ? 1 : -1;

    $oldAccount->balance -= $oldSign * $transaction->amount;
    $oldAccount->save();

    $newAccount->balance += $newSign * $request->amount;
    $newAccount->save();

    $transaction->update([
        'account_id' => $newAccount->id,
        'category_id' => $category->id,
        'type' => $request->type,
        'amount' => $request->amount,
        'description' => $request->description,
        'date' => $request->date,
    ]);

    return back()->with('status', 'Transaksi berhasil diperbarui.');
})->middleware('auth')->name('transaksi.update');

Route::post('/saldo/account/add', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'type' => 'required|in:debit,cash',
        'name' => 'required|string|max:255',
        'account_number' => 'nullable|string|max:255',
        'balance' => 'required|numeric|min:0',
    ]);

    Auth::user()->accounts()->create([
        'type' => $request->type,
        'name' => $request->name,
        'account_number' => $request->account_number ?: null,
        'balance' => $request->balance,
    ]);

    return back()->with('status', 'Akun berhasil ditambahkan.');
})->middleware('auth')->name('saldo.account.add');

Route::post('/saldo/account/update', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'account_id' => 'required|integer',
        'type' => 'required|in:debit,cash',
        'name' => 'required|string|max:255',
        'account_number' => 'nullable|string|max:255',
        'balance' => 'required|numeric|min:0',
    ]);

    $account = Auth::user()->accounts()->find($request->account_id);
    if (!$account) {
        return back()->with('error', 'Akun tidak ditemukan.');
    }

    $account->update([
        'type' => $request->type,
        'name' => $request->name,
        'account_number' => $request->account_number ?: null,
        'balance' => $request->balance,
    ]);

    return back()->with('status', 'Data akun berhasil diperbarui.');
})->middleware('auth')->name('saldo.account.update');

Route::post('/saldo/account/remove', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $request->validate([
        'account_id' => 'required|integer',
    ]);

    $user = Auth::user();
    $account = $user->accounts()->find($request->account_id);
    if (!$account) {
        return back()->with('error', 'Akun tidak ditemukan.');
    }

    if ($user->accounts()->count() <= 1) {
        return back()->with('error', 'Minimal harus menjaga satu akun aktif.');
    }

    $account->delete();
    return back()->with('status', 'Akun berhasil dihapus.');
})->middleware('auth')->name('saldo.account.remove');

Route::redirect('/', '/login');
// Register
Route::post('/register', [AuthController::class, 'register']);
// Login
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login')->with('status', 'Logout berhasil.');
})->name('logout');

// Forget Password
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/otp', [AuthController::class, 'showOtp'])->name('otp');
Route::post('/otp', [AuthController::class, 'verifyOtp']);
Route::get('/createpass', [AuthController::class, 'showCreatePass'])->name('createpass');
Route::post('/createpass', [AuthController::class, 'resetPassword']);

