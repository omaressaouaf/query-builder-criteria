<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Criteria;

use Omaressaouaf\QueryBuilderCriteria\Criteria as BaseCriteria;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;

class Criteria extends BaseCriteria
{
    protected array $filters = ['filter_1', 'filter_2'];

    protected array $exactFilters = ['ex_filter_1', 'ex_filter_2'];

    protected array $belongsToFilters = ['bg_filter_1', 'bg_filter_2'];

    protected array $scopeFilters = ["sc_filter_1", 'sc_filter_2'];

    protected string|bool|array|null $trashedFilter = true;

    protected array $defaultSorts = ['default_sort_1'];

    protected array $sorts = ['sort_1', '-sort_2'];

    protected array $includes = ['include_1', 'include_2'];

    protected array $countIncludes = ['ct_include_1', 'ct_include_2'];

    protected array $existsIncludes = ['es_include_1', 'es_include_2'];

    protected array $defaultFields = ['df_field_1', 'df_field_2'];

    protected array $fields = ['field_1', 'field_2'];

    protected array $searches = ['search_1', 'search_2'];

    protected array $fullTextSearches = ['ft_search_1', 'ft_search_2'];

    protected ?bool $splitSearchIntoTerms = true;

    protected function advancedFilters(): array
    {
        return [
            AllowedFilter::callback('cb_filter_1', fn() => '')
        ];
    }

    protected function advancedSorts(): array
    {
        return [
            AllowedSort::callback('cb_sort_1', fn() => '')
        ];
    }

    protected function advancedIncludes(): array
    {
        return [
            AllowedInclude::callback('cb_include', fn() => '')
        ];
    }
}
