<?php

namespace Omaressaouaf\QueryBuilderCriteria\Traits;

use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedSort;

trait TransformsSorts
{
    public function getDefaultSorts(): array
    {
        return array_merge(
            $this->transformDefaultSorts(),
            $this->advancedDefaultSorts()
        );
    }

    public function getSorts(): array
    {
        return array_merge(
            $this->transformSorts(),
            $this->advancedSorts()
        );
    }

    public function transformDefaultSorts(): array
    {
        return
            Arr::map(
                Arr::wrap($this->defaultSorts),
                function (string $value, mixed $key) {
                    if (is_numeric($key)) {
                        return AllowedSort::field($value);
                    }

                    return AllowedSort::field($key, $value);
                }
            );
    }

    private function transformSorts(): array
    {
        return Arr::map(
            $this->sorts,
            function (string $value, mixed $key) {
                if (is_numeric($key)) {
                    return AllowedSort::field($value);
                }

                return AllowedSort::field($key, $value);
            }
        );
    }
}
