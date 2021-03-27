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
        if ($this->app->runningInConsole()) {

            // Publish the model
            $this->publishes([
                __DIR__.'/Models/Medium.php' => app_path('models/Medium.php'),
            ], 'model');

            // Publish the config
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('kusikusi_media.php'),
            ], 'config');

            // Publish Laravel route
            $this->publishes([
            __DIR__.'/../routes/media.php' => base_path('routes/kusikusi_media.php'),
            ], 'routes');

            // Publish Laravel route
            $this->publishes([
            __DIR__.'/../routes/media_api.php' => base_path('routes/kusikusi_media_api.php'),
            ], 'api_routes');
            
            // Publish Lumen route
            /* $this->publishes([
            __DIR__.'/../routes/media-lumen.php' => base_path('routes/media.php'),
            ], 'route-lumen'); */
        }
    }
}
