<?php

namespace FreddieGar\Rbac\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
class RbacServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/rbac.php', 'rbac');
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
//        $this->loadRoutesFrom(__DIR__ . '/../config/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}
