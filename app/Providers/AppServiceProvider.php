<?php

namespace App\Providers;

use App\Contracts\Repositories\LoginRepository;
use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\EloquentLoginRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
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
            LoginRepository::class => EloquentLoginRepository::class,
            UserRepository::class => EloquentUserRepository::class,
        ];

        foreach ($repositories as $interface => $concrete) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->app->bind($interface, $concrete);
        }
    }
}
