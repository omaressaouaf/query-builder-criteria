<?php

namespace Omaressaouaf\QueryBuilderCriteria\Traits;

trait TransformsFields
{
    public function getDefaultFields(): array
    {
        return $this->defaultFields;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
