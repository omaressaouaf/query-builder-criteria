<?php

namespace Omaressaouaf\QueryBuilderCriteria\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilderRequest;

trait HandlesSearch
{
    private const RELATION_FULL_TEXT_PREFIX = 'full_text_';

    public function handleSearch(Builder $query, mixed $search, bool $splitIntoTerms = false): Builder
    {
        $search = $this->normalizeSearch($search);

        if (! $search) {
            return $query;
        }

        $searchTerms = $splitIntoTerms ? $this->getSearchTerms($search) : [$search];

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

    public function searchThroughColumns(Builder $query, string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        foreach ($this->getSearchableColumns() as $column) {
            $this->searchableIsFullText($column)
                ? $query->orWhereFullText($column, $term)
                : $query->orWhere($column, 'like', '%' . $term . '%');
        }

        return $query;
    }

    public function searchThroughRelations(Builder $query, string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $query->orWhere(function ($query) use ($term) {
            foreach ($this->getSearchableRelations() as $relation => $columns) {
                $query
                    ->orWhereHas(
                        $relation,
                        function ($query) use ($relation, $columns, $term) {
                            foreach ($columns as $key => $column) {
                                $chainingWhere = $key === 0 ? 'where' : 'orWhere';

                                $this->searchableIsFullText($column, $relation)
                                    ? $query->{$chainingWhere . 'FullText'}($column, $term)
                                    : $query->{$chainingWhere}($column, 'like', '%' . $term . '%');
                            }
                        }
                    );
            }
        });

        return $query;
    }

    public function sortByFullTextRelevance(Builder $query, string $search): Builder
    {
        if (! isset($this->fullTextSearchable)) {
            return $query;
        }

        $fullTextSearchable = Arr::wrap($this->fullTextSearchable);

        if (! $search || ! count($fullTextSearchable)) {
            return $query;
        }

        $fullTextSearchable = implode(',', $fullTextSearchable);

        return $query
            ->when(! $query->getQuery()->columns, fn(Builder $query) => $query->select('*'))
            ->selectRaw("MATCH($fullTextSearchable) AGAINST(?) AS relevance", [$search])
            ->orderBy('relevance', 'desc');
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

    public function getSearchableColumns(): array
    {
        $searches = isset($this->searches) ? $this->searches : [];
        $fullTextSearches = isset($this->fullTextSearches) ? $this->fullTextSearches : [];

        $columnSearches = Arr::where(
            $searches,
            fn(string $search) => !str($search)->contains('.')
        );
        $columnsFullTextSearches = Arr::where(
            $fullTextSearches,
            fn(string $fullTextSearch) => !str($fullTextSearch)->contains('.')
        );

        return array_merge($columnSearches, $columnsFullTextSearches);
    }

    public function getSearchableRelations(): array
    {
        $searches = isset($this->searches) ? $this->searches : [];
        $fullTextSearches = isset($this->fullTextSearches) ? $this->fullTextSearches : [];

        $relationsSearches = Arr::where(
            $searches,
            fn(string $search) => str($search)->contains('.')
        );
        $relationsFullTextSearches = Arr::where(
            $fullTextSearches,
            fn(string $fullTextSearch) => str($fullTextSearch)->contains('.')
        );

        $formatRelations = fn(array $rs) => collect($rs)
            ->mapWithKeys(fn($value) => [Str::beforeLast($value, '.') => Str::afterLast($value, '.')])
            ->toArray();

        $formatRelations = function (array $rs) {
            $formatted = [];

            foreach ($rs as $relation) {
                $key = Str::beforeLast($relation, '.');
                $formatted[$key][] = Str::afterLast($relation, '.');
            }

            return $formatted;
        };

        return array_merge(
            $formatRelations($relationsSearches),
            $formatRelations($relationsFullTextSearches)
        );
    }

    public function searchableIsFullText(string $column, ?string $relation = null): bool
    {
        $fullTextSearches = isset($this->fullTextSearches) ? $this->fullTextSearches : [];

        if ($relation) {
            return in_array($relation . '.' . $column, Arr::wrap($fullTextSearches));
        }

        return in_array($column, $fullTextSearches);
    }
}
