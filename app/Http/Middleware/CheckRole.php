<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Permite acceso solo si el usuario tiene el rol indicado.
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = session('user');

        // Si no hay usuario o el rol no coincide, redirigir al home
        if (!$user || ($user['role'] ?? null) !== $role) {
            return redirect('/');
        }

        return $next($request);
    }
}