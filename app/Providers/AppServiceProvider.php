<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind de repositorios JSON
        $this->app->bind(
            \App\Contracts\IUserRepository::class,
            \App\Repositories\Json\UserRepositoryJson::class
        );

        $this->app->bind(
            \App\Contracts\IMessageRepository::class,
            \App\Repositories\Json\MessageRepositoryJson::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}