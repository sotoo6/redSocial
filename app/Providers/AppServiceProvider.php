<?php

/**
 * Service Provider principal de la aplicación.
 *
 * Registra bindings del contenedor de dependencias (interfaces -> implementaciones).
 *
 * @package App\Providers
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\IUserRepository;
use App\Contracts\IMessageRepository;
use App\Repositories\Db\UserRepositoryDb;
use App\Repositories\Db\MessageRepositoryDb;

/**
 * Proveedor de servicios de la aplicación.
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios y bindings en el contenedor IoC.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepositoryDb::class);
        $this->app->bind(IMessageRepository::class, MessageRepositoryDb::class);
    }

    /**
     * Arranque del proveedor de servicios.
     *
     * @return void
     */

    public function boot(): void
    {
        //
    }
}