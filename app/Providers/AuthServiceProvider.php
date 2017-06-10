<?php

namespace App\Providers;

use App\Contracts\Repositories\UserRepository;
use App\Entities\UserEntity;
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
        $this->app['auth']->viaRequest('api',
            function ($request) {
                $keyApiToken = UserEntity::KEY_API_TOKEN;
                if ($request->input($keyApiToken)) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    return UserRepository::getByApiToken($request->input($keyApiToken));
                }

                return null;
            }
        );

        return true;
    }
}
