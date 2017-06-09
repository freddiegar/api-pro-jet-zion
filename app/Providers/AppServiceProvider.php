<?php

namespace App\Providers;

use App\Contracts\Repositories\LoginRepository;
use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\EloquentLoginRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $repositories = [
            UserRepository::class => EloquentUserRepository::class,
            LoginRepository::class => EloquentLoginRepository::class,
        ];

        foreach ($repositories as $interface => $concrete) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->app->bind($interface, $concrete);
        }
    }
}
