<?php

namespace Kusikusi;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'kusikusi_api');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Publish the config
            /* $this->publishes([
                __DIR__.'/../config/config.php' => config_path('kusikusi_api.php'),
            ], 'config'); */


            // Publish Laravel route
            $this->publishes([
            __DIR__.'/../routes/website-laravel.php' => base_path('routes/kusikusi_api.php'),
            ], 'route');
            
            // Publish Lumen route
            /* $this->publishes([
            __DIR__.'/../routes/website-lumen.php' => base_path('routes/kusikusi_api.php'),
            ], 'route-lumen'); */
        }
    }
}
