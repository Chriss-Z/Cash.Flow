<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RememberLoginExpiry
{
  public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

            $user = Auth::user();

            if ($user->remember_login_started_at) {

                if ($user->remember_login_started_at->diffInMinutes(now()) >= 1) {

                    $user->remember_login_started_at = null;
                    $user->save();

                    Auth::logout();

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->with('error', 'Keep me signed in telah berakhir.');
                }

            }

        }

        return $next($request);
    }
}
