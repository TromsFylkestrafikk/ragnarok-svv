<?php

namespace Ragnarok\StatensVegvesen;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Ragnarok\StatensVegvesen\Sinks\SinkStatensVegvesen;
use Ragnarok\Sink\Facades\SinkRegistrar;

class StatensVegvesenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ragnarok_statens_vegvesen.php', 'ragnarok_statens_vegvesen');
        $this->publishConfig();

        SinkRegistrar::register(SinkStatensVegvesen::class);

        // $this->loadViewsFrom(__DIR__.'/resources/views', 'ragnarok_statens_vegvesen');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
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
                __DIR__ . '/../config/ragnarok_statens_vegvesen.php' => config_path('ragnarok_statens_vegvesen.php'),
            ], ['config', 'config-statensVegvesen', 'statensVegvesen']);
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
            'namespace'  => "Ragnarok\StatensVegvesen\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
        ];
    }
}
