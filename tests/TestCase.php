<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Omaressaouaf\QueryBuilderCriteria\QueryBuilderCriteriaServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Omaressaouaf\\QueryBuilderCriteria\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
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
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // foreach (File::allFiles(__DIR__ . '/../database/migrations') as $migration) {
        //     (include $migration->getRealPath())->up();
        // }
    }
}
