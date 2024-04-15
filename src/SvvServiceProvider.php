<?php

namespace Ragnarok\Svv;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Ragnarok\Svv\Sinks\SinkSvv;
use Ragnarok\Sink\Facades\SinkRegistrar;

class SvvServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ragnarok_svv.php', 'ragnarok_svv');
        $this->publishConfig();

        SinkRegistrar::register(SinkSvv::class);

        // $this->loadViewsFrom(__DIR__.'/resources/views', 'ragnarok_svv');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->registerRoutes();
    }

    /**
     * Publish Config
     *
     * @return void
     */
    public function publishConfig(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ragnarok_svv.php' => config_path('ragnarok_svv.php'),
            ], ['config', 'config-svv', 'svv']);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
    * Get route group configuration array.
    *
    * @return string[]
    */
    protected function routeConfiguration(): array
    {
        return [
            'namespace'  => "Ragnarok\Svv\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
        ];
    }
}
