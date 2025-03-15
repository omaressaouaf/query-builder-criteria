<?php

namespace Omaressaouaf\QueryBuilderCriteria;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Spatie\QueryBuilder\QueryBuilder;

trait QueryableByCriteria
{
    public function scopeQueryByCriteria($query, array|string $criteria = [])
    {
        $criteria = collect(
            array_merge(
                Arr::wrap($criteria),
                $this->defaultQueryBuilderCriteria()
            )
        )
            ->map(function ($criteria) {
                $criteria = new $criteria;

                if (!$criteria instanceof Criteria) {
                    throw new InvalidArgumentException(
                        'criteria needs to be instance of ' . Criteria::class
                    );
                }

                return $criteria;
            });

        $getPropery = fn($cb) => $criteria->flatMap($cb)->values()->toArray();
        [
            $filters,
            $sorts,
            $defaultSorts,
            $includes,
            $fields,
            $defaultFields,
        ] = [
            $getPropery(fn(Criteria $c) => $c->getAllFilters()),
            $getPropery(fn(Criteria $c) => $c->getSorts()),
            $getPropery(fn(Criteria $c) => $c->getDefaultSorts()),
            $getPropery(fn(Criteria $c) => $c->getAllIncludes()),
            $getPropery(fn(Criteria $c) => $c->getFields()),
            $getPropery(fn(Criteria $c) => $c->getDefaultFields()),
        ];

        $query = QueryBuilder::for($query);

        if (count($filters)) {
            $query = $query->allowedFilters($filters);
        }

        if (count($sorts)) {
            $query = $query->allowedSorts($sorts);
        }

        $defaultSorts = count($defaultSorts)
            ? $defaultSorts
            : config('query-builder-criteria.default_sorts') ?? [];
        if (count($defaultSorts)) {
            $query = $query->defaultSorts($defaultSorts);
        }

        if (count($includes)) {
            $query = $query->allowedIncludes($includes);
        }

        $requestFields = request()->get(config('query-builder.parameters.fields'));
        if ($requestFields && count($fields)) {
            $query = $query->allowedFields($fields);
        } elseif (!$requestFields && count($defaultFields)) {
            $query = $query->addSelect($defaultFields);
        }

        return $query;
    }

    protected function defaultQueryBuilderCriteria(): array
    {
        return [];
    }
}
