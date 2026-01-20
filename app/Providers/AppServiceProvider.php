<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\IUserRepository;
use App\Contracts\IMessageRepository;
use App\Repositories\Json\UserRepositoryJson;
use App\Repositories\Json\MessageRepositoryJson;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepositoryJson::class);
        $this->app->bind(IMessageRepository::class, MessageRepositoryJson::class);
    }

    public function boot(): void
    {
        //
    }
}