<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('auth_user_uuid')) {
            return redirect('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
