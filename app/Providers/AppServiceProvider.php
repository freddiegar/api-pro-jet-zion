<?php

namespace App\Providers;

use App\Contracts\Repositories\LoginRepository;
use App\Contracts\Repositories\UserRepository;
use App\Repositories\Eloquent\EloquentLoginRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use FreddieGar\Rbac\Contracts\Repositories\PermissionRepository;
use FreddieGar\Rbac\Contracts\Repositories\RolePermissionRepository;
use FreddieGar\Rbac\Contracts\Repositories\RoleRepository;
use FreddieGar\Rbac\Contracts\Repositories\UserRoleRepository;
use FreddieGar\Rbac\Repositories\Eloquent\EloquentPermissionRepository;
use FreddieGar\Rbac\Repositories\Eloquent\EloquentRolePermissionRepository;
use FreddieGar\Rbac\Repositories\Eloquent\EloquentRoleRepository;
use FreddieGar\Rbac\Repositories\Eloquent\EloquentUserRoleRepository;
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
