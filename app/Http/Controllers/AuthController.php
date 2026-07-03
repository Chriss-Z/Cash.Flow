<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    //Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

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

        $otp = rand(100000, 999999);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => now(),
            ]
        );

        Mail::to($request->email)->send(new OtpMail($otp));

        session(['otp_email' => $request->email]);
        session(['otp_flow' => 'register']);

        return redirect()->route('otp', ['email' => $request->email, 'flow' => 'register'])
            ->with('status', 'Kode OTP berhasil dikirim ke email Anda. Silakan cek email untuk melanjutkan.');
    }
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Email atau password salah')->withInput();
        }

        $remember = $request->has('remember');

        Auth::login($user, $remember);

        $request->session()->regenerate();

        if ($remember) {

            $user->update([
                'remember_login_started_at' => now()
            ]);
        } else {

            $user->update([
                'remember_login_started_at' => null
            ]);
        }

        return redirect()->route('beranda');
    }
    // Forget Password
    public function forgotPassword(Request $request)
    {
        // cek email sama
        if ($request->email !== $request->confirm_email) {
            return back()->with('error', 'Email tidak sama')->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan')->withInput();
        }
        // OTP
        $otp = rand(100000, 999999);

        // simpan ke database
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => now()
            ]
        );
        // Kirim mail
        Mail::to($request->email)->send(new OtpMail($otp));
        return redirect()->route('otp', ['email' => $request->email])->with('status', 'Kode OTP berhasil dikirim ke email Anda.');
    }

    public function showOtp(Request $request)
    {
        if ($request->has('email')) {
            session(['otp_email' => $request->email]);
        }

        if ($request->has('flow')) {
            session(['otp_flow' => $request->flow]);
        }

        $email = old('email', session('otp_email'));
        $flow = old('flow', session('otp_flow', 'forgot'));

        return view('Auth.otp', compact('email', 'flow'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'flow' => 'required|string|in:forgot,register',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Email tidak ditemukan atau OTP sudah kadaluarsa.'])->withInput();
        }

        if ($reset->token !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak sesuai untuk email ini.'])->withInput();
        }

        if ($request->flow === 'register') {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return back()->withErrors(['email' => 'Email tidak ditemukan.'])->withInput();
            }

            $user->email_verified_at = now();
            $user->save();

            DB::table('password_resets')->where('email', $request->email)->delete();
            session()->forget(['otp_email', 'otp_flow']);

            Auth::login($user, false);
            $user->update([
                'remember_login_started_at' => null
            ]);
            return redirect()->route('beranda')->with('status', 'Registrasi berhasil. Selamat datang!');
        }

        session(['reset_email' => $request->email]);

        return redirect()->route('createpass');
    }
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            $user->remember_login_started_at = null;
            $user->save();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showCreatePass(Request $request)
    {
        $email = session('reset_email') ?? $request->query('email');

        if (!$email) {
            return redirect()->route('otp')->with('error', 'Silakan verifikasi OTP terlebih dahulu.');
        }

        return view('createpass', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if (session('reset_email') !== $request->email) {
            return back()->with('error', 'Email tidak cocok dengan sesi reset password.')->withInput();
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan.')->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();
        session()->forget(['reset_email', 'otp_email']);

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui. Silakan login dengan kredensial baru.');
    }
}
