<?php

namespace Omaressaouaf\QueryBuilderCriteria\Traits;

trait TransformsFields
{
    public function getAllFields(): array
    {
        return array_merge(
            $this->defaultFields,
            $this->fields
        );
    }
}
