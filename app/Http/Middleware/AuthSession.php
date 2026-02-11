<?php

/**
 * Middleware de autenticación por sesión.
 *
 * Restringe el acceso a rutas protegidas si no existe un usuario en sesión.
 *
 * @package App\Http\Middleware
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware que exige sesión iniciada.
 *
 * @package App\Http\Middleware
 */
class AuthSession
{
    /**
     * Maneja la petición entrante.
     *
     * Si no hay usuario en sesión, redirige a /login (si es home) o muestra la
     * vista de acceso restringido con código 401.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
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