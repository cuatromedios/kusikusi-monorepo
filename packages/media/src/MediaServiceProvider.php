<?php

namespace Kusikusi;

use Illuminate\Support\ServiceProvider;

class MediaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'kusikusi_media');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/media_api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/media.php');
        if ($this->app->runningInConsole()) {
            // Publish the model
            $this->publishes([
                __DIR__.'/Models/Medium.php' => app_path('models/Medium.php'),
            ], 'model');

            // Publish the config
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('kusikusi_media.php'),
            ], 'config');
        }
    }
}
