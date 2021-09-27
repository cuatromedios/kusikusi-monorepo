<?php

namespace Kusikusi;

use Illuminate\Support\ServiceProvider;

class ModelsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'kusikusi_models');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/models.php');
        if ($this->app->runningInConsole()) {
            // Export the config
            $this->publishes([
              __DIR__.'/../config/config.php' => config_path('kusikusi_models.php'),
            ], 'config');
        }
    }
}
