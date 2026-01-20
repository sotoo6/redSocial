<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware global que se ejecuta en todas las peticiones.
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Grupos de middleware para las rutas web y API.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,

            // Necesario para mostrar errores de sesión en Blade
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // Protección contra CSRF
            \App\Http\Middleware\VerifyCsrfToken::class,

            // Auto-binding de modelos en rutas
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Middleware asignables manualmente en rutas individuales.
     */
    protected $routeMiddleware = [

        // Middleware nativos de Laravel
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,

        // MIDDLEWARE PERSONALIZADOS

        // Middleware propio equivalente a comprobar $_SESSION['user']
        'authsession' => \App\Http\Middleware\AuthSession::class,

        // Middleware para profesores (se creará luego)
        'role' => \App\Http\Middleware\CheckRole::class,
    ];
}