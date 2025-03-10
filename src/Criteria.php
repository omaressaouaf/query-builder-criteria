<?php

namespace Omaressaouaf\QueryBuilderCriteria;

use Omaressaouaf\QueryBuilderCriteria\Traits\TransformsFields;
use Omaressaouaf\QueryBuilderCriteria\Traits\TransformsFilters;
use Omaressaouaf\QueryBuilderCriteria\Traits\TransformsIncludes;
use Omaressaouaf\QueryBuilderCriteria\Traits\TransformsSorts;

class Criteria
{
    use TransformsFilters;
    use TransformsSorts;
    use TransformsIncludes;
    use TransformsFields;

    protected array $partialFilters = [];

    protected array $exactFilters = [];

    protected array $belongsToFilters = [];

    protected array $scopeFilters = [];

    protected string|bool|array|null $trashedFilter = null;

    protected array $defaultSorts = [];

    protected array $sorts = [];

    protected array $relationshipIncludes = [];

    protected array $countIncludes = [];

    protected array $existsIncludes = [];

    protected array $defaultFields = [];

    protected array $fields = [];

    protected function advancedFilters(): array
    {
        return [];
    }

    protected function advancedSorts(): array
    {
        return [];
    }

    protected function advancedIncludes(): array
    {
        return [];
    }
}
