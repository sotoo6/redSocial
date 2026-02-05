<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\IUserRepository;
use App\Contracts\IMessageRepository;
use App\Repositories\Db\UserRepositoryDb;
use App\Repositories\Db\MessageRepositoryDb;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepositoryDb::class);
        $this->app->bind(IMessageRepository::class, MessageRepositoryDb::class);
    }

    public function boot(): void
    {
        //
    }
}