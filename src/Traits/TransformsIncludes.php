<?php

namespace Omaressaouaf\QueryBuilderCriteria\Traits;

use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedInclude;

trait TransformsIncludes
{
    public function getAllIncludes(): array
    {
        return array_merge(
            $this->transformIncludes(),
            $this->transformCountIncludes(),
            $this->transformExistsIncludes(),
            collect($this->advancedIncludes())->flatten()->toArray()
        );
    }

    private function transformIncludes(): array
    {
        return collect(
            Arr::map(
                $this->includes,
                function (string $value, mixed $key) {
                    if (is_numeric($key)) {
                        return AllowedInclude::relationship($value);
                    }

                    return AllowedInclude::relationship($key, $value);
                }
            )
        )
            ->flatten()
            ->toArray();
    }

    private function transformCountIncludes(): array
    {
        return collect(
            Arr::map(
                $this->countIncludes,
                function (string $value, mixed $key) {
                    if (is_numeric($key)) {
                        return AllowedInclude::count($value);
                    }

                    return AllowedInclude::count($key, $value);
                }
            )
        )
            ->flatten()
            ->toArray();
    }

    private function transformExistsIncludes(): array
    {
        return collect(
            Arr::map(
                $this->existsIncludes,
                function (string $value, mixed $key) {
                    if (is_numeric($key)) {
                        return AllowedInclude::exists($value);
                    }

                    return AllowedInclude::exists($key, $value);
                }
            )
        )
            ->flatten()
            ->toArray();
    }
}
