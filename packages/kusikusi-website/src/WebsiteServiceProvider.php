<?php

namespace Kusikusi;

use Illuminate\Support\ServiceProvider;

class WebsiteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Publish controllers
            $this->publishes([
            __DIR__.'/../src/Http/Controllers/HtmlController.php' => app_path('Http/Controllers/HtmlController.php'),
            ]);
        
        }
    }
}
