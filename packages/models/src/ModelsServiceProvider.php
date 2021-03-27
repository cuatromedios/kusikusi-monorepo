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
        if ($this->app->runningInConsole()) {
            // Export the config
            $this->publishes([
              __DIR__.'/../config/config.php' => config_path('kusikusi_models.php'),
            ], 'config');
            // Export the migrations
            if (! class_exists('CreateEntitiesTable')) {
                $this->publishes([
                __DIR__ . '/../database/migrations/create_entities_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_entities_table.php'),
                __DIR__ . '/../database/migrations/create_entities_contents_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()+1) . '_create_entities_contents_table.php'),
                __DIR__ . '/../database/migrations/create_entities_relations_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()+2) . '_create_entities_relations_table.php'),
                __DIR__ . '/../database/migrations/create_entities_archives_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()+3) . '_create_entities_archives_table.php'),
                __DIR__ . '/../database/migrations/create_entities_routes_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()+4) . '_create_entities_routes_table.php'),
                ], 'migrations');
            }
            // Publish Laravel route
            $this->publishes([
                __DIR__.'/../routes/models.php' => base_path('routes/kusikusi_models_api.php'),
                ], 'route');
        }
    }
}
