<?php

namespace Inweb\Tools\PermissionsTool;

use InWeb\Admin\App\Admin;
use InWeb\Admin\App\AdminRoute;
use InWeb\Admin\App\Events\ServingAdmin;
use Illuminate\Support\ServiceProvider;
use Inweb\Tools\PermissionsTool\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Admin::serving(function (ServingAdmin $event) {
            Admin::script('permissions-tool', __DIR__.'/../dist/js/tool.js');
            Admin::style('permissions-tool', __DIR__.'/../dist/css/tool.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        AdminRoute::api('Inweb\Tools\PermissionsTool\Http\Controllers', function () {
            \Route::middleware([Authorize::class])->group(function() {
                $this->registerRoutes();
            });
        });
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }
}
