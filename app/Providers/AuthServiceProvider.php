<?php

namespace App\Providers;

use App\Contracts\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return bool
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        /** @noinspection PhpUndefinedMethodInspection */
        $this->app['auth']->viaRequest('api', /** @noinspection PhpInconsistentReturnPointsInspection */
        function ($request) {
            /** @noinspection PhpUndefinedMethodInspection */
            if ($request->input('api_token')) {
                /** @noinspection PhpUndefinedMethodInspection */
                return UserRepository::getByApiToken($request->input('api_token'));
            }
        });

        return true;
    }
}
