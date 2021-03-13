<?php

namespace Kusikusi\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Kusikusi\ModelsServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__ . '/../database/migrations/create_entities_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_entities_contents_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_entities_relations_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_entities_archives_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_entities_routes_table.php.stub';
        (new \CreateEntitiesTable)->up();
        (new \CreateEntitiesContentsTable)->up();
        (new \CreateEntitiesRelationsTable)->up();
        (new \CreateEntitiesArchivesTable)->up();
        (new \CreateEntitiesRoutesTable)->up();
    }
}
