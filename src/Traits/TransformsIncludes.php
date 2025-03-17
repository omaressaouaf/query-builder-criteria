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
            $this->advancedIncludes(),
        );
    }

    private function transformIncludes(): array
    {
        return Arr::map(
            $this->includes,
            function (string $value, mixed $key) {
                if (is_numeric($key)) {
                    return AllowedInclude::relationship($value);
                }

                return AllowedInclude::relationship($key, $value);
            }
        );
    }

    private function transformCountIncludes(): array
    {
        return Arr::map(
            $this->countIncludes,
            function (string $value, mixed $key) {
                if (is_numeric($key)) {
                    return AllowedInclude::count($value);
                }

                return AllowedInclude::count($key, $value);
            }
        );
    }

    private function transformExistsIncludes(): array
    {
        return Arr::map(
            $this->existsIncludes,
            function (string $value, mixed $key) {
                if (is_numeric($key)) {
                    return AllowedInclude::exists($value);
                }

                return AllowedInclude::exists($key, $value);
            }
        );
    }
}
