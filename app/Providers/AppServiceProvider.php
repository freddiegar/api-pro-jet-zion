<?php

namespace App\Providers;

use App\Contracts\Repositories\LoginRepository;
use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\EloquentLoginRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use FreddieGar\Base\Providers\ExtraValidator;
use FreddieGar\Rbac\Contracts\Repositories\PermissionRepository;
use FreddieGar\Rbac\Contracts\Repositories\RolePermissionRepository;
use FreddieGar\Rbac\Contracts\Repositories\RoleRepository;
use FreddieGar\Rbac\Contracts\Repositories\UserRoleRepository;
use FreddieGar\Rbac\Repositories\Eloquent\EloquentPermissionRepository;
use FreddieGar\Rbac\Repositories\Eloquent\EloquentRolePermissionRepository;
use FreddieGar\Rbac\Repositories\Eloquent\EloquentRoleRepository;
use FreddieGar\Rbac\Repositories\Eloquent\EloquentUserRoleRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new ExtraValidator($translator, $data, $rules, $messages);
        });
        /** @noinspection PhpUnusedParameterInspection */
        Validator::replacer('both_not_filled', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':another', $parameters[0], $message);
        });
    }

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
            // Rbac
            RoleRepository::class => EloquentRoleRepository::class,
            PermissionRepository::class => EloquentPermissionRepository::class,
            RolePermissionRepository::class => EloquentRolePermissionRepository::class,
            UserRoleRepository::class => EloquentUserRoleRepository::class,
        ];

        foreach ($repositories as $interface => $concrete) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->app->bind($interface, $concrete);
        }
    }
}
