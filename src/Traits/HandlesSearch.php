<?php

namespace Omaressaouaf\QueryBuilderCriteria\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilderRequest;

trait HandlesSearch
{
    private function handleSearch(Builder $query, mixed $search): Builder
    {
        $search = $this->normalizeSearch($search);

        if (! $search) {
            return $query;
        }

        $splitSearchIntoTerms = is_bool($this->splitSearchIntoTerms)
            ? $this->splitSearchIntoTerms
            : config('query-builder-criteria.split_search_into_terms') ?? false;

        $searchTerms = $splitSearchIntoTerms ? $this->getSearchTerms($search) : [$search];

        foreach ($searchTerms as $key => $term) {
            $query->{$key === 0 ? 'where' : 'orWhere'}(
                function (Builder $query) use ($term) {
                    $query = $this->searchThroughColumns($query, $term);
                    $query = $this->searchThroughRelations($query, $term);
                }
            );
        }

        $query = $this->sortByFullTextRelevance($query, $search);

        return $query;
    }

    private function normalizeSearch(array|string $search): string
    {
        return is_array($search)
            ? implode(QueryBuilderRequest::getFilterArrayValueDelimiter(), $search)
            : $search;
    }

    private function getSearchTerms(string $search): array
    {
        // We explode the search into search terms when there are spaces only if it starts and finishes with double quotes (inspired by google) . e.g : ("search exactly for this entire phrase")
        if (Str::startsWith($search, '"') && Str::endsWith($search, '"')) {
            return [Str::of($search)->replaceFirst('"', '')->replaceLast('"', '')->__toString()];
        } else {
            return explode(' ', $search);
        }
    }

    private function searchThroughColumns(Builder $query, string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        foreach ($this->getColumnsSearches() as $column) {
            $this->searchIsFullText($column)
                ? $query->orWhereFullText($column, $term)
                : $query->orWhere($column, 'like', '%' . $term . '%');
        }

        return $query;
    }

    private function searchThroughRelations(Builder $query, string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $query->orWhere(function ($query) use ($term) {
            foreach ($this->getRelationsSearches() as $relation => $columns) {
                $query
                    ->orWhereHas(
                        $relation,
                        function ($query) use ($relation, $columns, $term) {
                            foreach ($columns as $key => $column) {
                                $chainingWhere = $key === 0 ? 'where' : 'orWhere';

                                $this->searchIsFullText($column, $relation)
                                    ? $query->{$chainingWhere . 'FullText'}($column, $term)
                                    : $query->{$chainingWhere}($column, 'like', '%' . $term . '%');
                            }
                        }
                    );
            }
        });

        return $query;
    }

    private function sortByFullTextRelevance(Builder $query, string $search): Builder
    {
        if (! $search || ! count($this->fullTextSearches)) {
            return $query;
        }

        $fullTextSearches = implode(',', $this->fullTextSearches);

        return $query
            ->when(! $query->getQuery()->columns, fn(Builder $query) => $query->select('*'))
            ->selectRaw("MATCH($fullTextSearches) AGAINST(?) AS relevance", [$search])
            ->orderBy('relevance', 'desc');
    }

    private function getColumnsSearches(): array
    {
        $columnsSearches = Arr::where(
            $this->searches,
            fn(string $search) => !str($search)->contains('.')
        );
        $columnsFullTextSearches = Arr::where(
            $this->fullTextSearches,
            fn(string $fullTextSearch) => !str($fullTextSearch)->contains('.')
        );

        return array_merge($columnsSearches, $columnsFullTextSearches);
    }

    private function getRelationsSearches(): array
    {
        $relationsSearches = Arr::where(
            $this->searches,
            fn(string $search) => str($search)->contains('.')
        );
        $relationsFullTextSearches = Arr::where(
            $this->fullTextSearches,
            fn(string $fullTextSearch) => str($fullTextSearch)->contains('.')
        );

        $formatRelationsSearches = function (array $rs) {
            $formatted = [];

            foreach ($rs as $rsI) {
                $key = Str::beforeLast($rsI, '.');
                $formatted[$key][] = Str::afterLast($rsI, '.');
            }

            return $formatted;
        };

        return array_merge(
            $formatRelationsSearches($relationsSearches),
            $formatRelationsSearches($relationsFullTextSearches)
        );
    }

    private function searchIsFullText(string $column, ?string $relation = null): bool
    {
        if ($relation) {
            return in_array($relation . '.' . $column, $this->fullTextSearches);
        }

        return in_array($column, $this->fullTextSearches);
    }
}
