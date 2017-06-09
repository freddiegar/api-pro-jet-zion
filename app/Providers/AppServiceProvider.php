<?php

namespace App\Providers;

use App\Contracts\Repositories\UserRepository;
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
            UserRepository::class => EloquentUserRepository::class
        ];

        foreach ($repositories as $interface => $concrete) {
            $this->app->bind($interface, $concrete);
        }
    }
}
