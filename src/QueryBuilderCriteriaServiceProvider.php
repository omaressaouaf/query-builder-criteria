<?php

namespace Omaressaouaf\QueryBuilderCriteria;

use Illuminate\Support\ServiceProvider;

class QueryBuilderCriteriaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/query-builder.php', 'query-builder');
        $this->mergeConfigFrom(__DIR__ . '/../config/query-builder-criteria.php', 'query-builder-criteria');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/query-builder-criteria.php' => config_path('query-builder-criteria.php'),
            ]);
        }
    }
}
