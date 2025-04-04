<?php

namespace Omaressaouaf\QueryBuilderCriteria\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;

trait TransformsFilters
{
    use HandlesSearch;

    public function getAllFilters(): array
    {
        $filters = array_merge(
            $this->transformFilters(),
            $this->transformExactFilters(),
            $this->transformBelongsToFilters(),
            $this->transformScopeFilters(),
            $this->advancedFilters(),
            [
                AllowedFilter::callback(
                    config('query-builder-criteria.search_query_parameter') ?? 'search_query',
                    fn (Builder $query, mixed $search) => $this->handleSearch($query, $search)
                ),
            ]
        );

        if (! is_null($this->trashedFilter)) {
            $name = match (true) {
                is_bool($this->trashedFilter) => 'trashed',
                is_string($this->trashedFilter) => $this->trashedFilter,
                is_array($this->trashedFilter) => array_key_first($this->trashedFilter),
            };

            $internalName = match (true) {
                is_array($this->trashedFilter) => $this->trashedFilter[array_key_first($this->trashedFilter)] ?? null,
                default => null
            };

            $filters[] = AllowedFilter::trashed($name, $internalName);
        }

        return $filters;
    }

    private function transformFilters(): array
    {
        return Arr::map(
            $this->filters,
            function (string $value, mixed $key) {
                if (is_numeric($key)) {
                    return AllowedFilter::partial($value);
                }

                return AllowedFilter::partial($key, $value);
            }
        );
    }

    private function transformExactFilters(): array
    {
        return Arr::map(
            $this->exactFilters,
            function (string $value, mixed $key) {
                if (is_numeric($key)) {
                    return AllowedFilter::exact($value);
                }

                return AllowedFilter::exact($key, $value);
            }
        );
    }

    private function transformBelongsToFilters(): array
    {
        return Arr::map(
            $this->belongsToFilters,
            function (string $value, mixed $key) {
                if (is_numeric($key)) {
                    return AllowedFilter::belongsTo($value);
                }

                return AllowedFilter::belongsTo($key, $value);
            }
        );
    }

    private function transformScopeFilters(): array
    {
        return Arr::map(
            $this->scopeFilters,
            function (string $value, mixed $key) {
                if (is_numeric($key)) {
                    return AllowedFilter::scope($value);
                }

                return AllowedFilter::scope($key, $value);
            }
        );
    }
}
