<?php

namespace App\Providers;

use App\Entities\UserEntity;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\UnauthorizedException;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
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
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        Auth::viaRequest('api',
            function (Request $request) {
                $apiToken = null;

                switch (true) {
                    // In input request
                    case $apiToken = $request->input(UserEntity::KEY_API_TOKEN):
                        break;
                    // In Header
                    case $apiToken = $request->header(UserEntity::KEY_API_TOKEN_HEADER):
                        break;
                    // In Authorization Header
                    default:
                        $Auth = explode(' ', $request->header(UserEntity::KEY_AUTHORIZATION_HEADER));
                        if (isset($Auth[1])) {
                            // Type Token
                            $apiToken = $Auth[1];
                        } elseif (isset($Auth[0])) {
                            // Token
                            $apiToken = $Auth[0];
                        }
                        break;
                }

                if ($apiToken) {
                    // TODO: Revise this funcionality, let interface UserRepository, not concrete EloquentUserRepository
                    /** @noinspection PhpUndefinedMethodInspection */
                    if ($user = EloquentUserRepository::getByApiToken($apiToken)) {
                        return $user;
                    }

                    throw new UnauthorizedException(trans('login.error.api_token'));
                }

                return null;
            }
        );
    }
}
