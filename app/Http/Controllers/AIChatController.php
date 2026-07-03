<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Target;
use App\Models\Bill;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

class AIChatController extends Controller
{
public function chat(Request $request)
{
    $message = $request->input('message');
   $user = Auth::user();

$userId = $user?->id ?? session('user_id');

if (!$userId) {
    $userId = 1;
}

    $saldo = Account::where(
    'user_id',
    $user->id
)->sum('balance');

$income = Transaction::where(
    'user_id',
    $user->id
)
->where(
    'type',
    'income'
)
->sum('amount');

$expense = Transaction::where(
    'user_id',
    $user->id
)
->where(
    'type',
    'expense'
)
->sum('amount');

$target = Target::where(
    'user_id',
    $user->id
)->sum('target_amount');

$bill = Bill::where(
    'user_id',
    $user->id
)->count();

    $context = "
Data keuangan user:

Saldo: Rp {$saldo}
Pemasukan: Rp {$income}
Pengeluaran: Rp {$expense}
Target: Rp {$target}
Tagihan aktif: {$bill}

Berikan jawaban singkat, jelas, dan berupa analisis keuangan.
";

    try {

        $result = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $context
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ]
        ]);

        $reply =
            $result
            ->choices[0]
            ->message
            ->content;

    } catch (Exception $e) {

        $analysis = "";

        if ($expense > $income) {
            $analysis .=
                "Pengeluaran lebih besar dari pemasukan. ";
        }

        if ($target == 0) {
            $analysis .=
                "Belum ada target keuangan. ";
        }

        if ($bill > 0) {
            $analysis .=
                "Ada {$bill} tagihan aktif.";
        }

        $reply =
            "AI sedang tidak tersedia.\n\n".
            "Saldo Rp ".number_format($saldo)."\n".
            "Pemasukan Rp ".number_format($income)."\n".
            "Pengeluaran Rp ".number_format($expense)."\n\n".
            $analysis;
    }

    return response()->json([
        'reply' => $reply
    ]);
}
}
