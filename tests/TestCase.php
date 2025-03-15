<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Omaressaouaf\QueryBuilderCriteria\QueryBuilderCriteriaServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            QueryBuilderCriteriaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver'   => 'mysql',
            'host' => 'localhost',
            'database' => 'query_builder_criteria_testing',
            'username' => 'root',
            'password' => '',
        ]);

        Artisan::call('migrate:rollback');

        foreach (File::allFiles(__DIR__.'/Migrations') as $migration) {
            $migration = include $migration->getRealPath();
            $migration->down();
            $migration->up();
        }
    }
}
