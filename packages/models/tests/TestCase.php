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
        include_once __DIR__ . '/../database/migrations/2021_09_28_090000_create_entities_table.php';
        include_once __DIR__ . '/../database/migrations/2021_09_28_090001_create_entities_relations_table.php';
        include_once __DIR__ . '/../database/migrations/2021_09_28_090002_create_entities_contents_table.php';
        include_once __DIR__ . '/../database/migrations/2021_09_28_090003_create_entities_archives_table.php';
        include_once __DIR__ . '/../database/migrations/2021_09_28_090004_create_entities_routes_table.php';
        (new \CreateEntitiesTable)->up();
        (new \CreateEntitiesContentsTable)->up();
        (new \CreateEntitiesRelationsTable)->up();
        (new \CreateEntitiesArchivesTable)->up();
        (new \CreateEntitiesRoutesTable)->up();
    }
}
