<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authsession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user')) {
            // En vez de redirect('/login'):
            return response()->view('auth.must_login', [], 403);
        }

        return $next($request);
    }
}