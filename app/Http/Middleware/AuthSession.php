<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authsession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user')) {

            // Si intenta entrar al home "/", lo mandamos al login
            if ($request->is('/')) {
                return redirect('/login');
            }

            // Para cualquier otra ruta protegida, mostramos la vista "must login"
            return response()->view('auth.must_login', [], 401);
        }

        return $next($request);
    }
}