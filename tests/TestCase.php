<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Omaressaouaf\QueryBuilderCriteria\QueryBuilderCriteriaServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            QueryBuilderServiceProvider::class,
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
    }
}
