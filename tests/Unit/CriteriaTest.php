<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Omaressaouaf\QueryBuilderCriteria\QueryableByCriteria;
use Omaressaouaf\QueryBuilderCriteria\Tests\Criteria\Criteria;
use Omaressaouaf\QueryBuilderCriteria\Tests\TestCase;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;

class CriteriaTest extends TestCase
{
    use RefreshDatabase, QueryableByCriteria;

    public function test_it_transforms_all_properties(): void
    {
        $criteria = new Criteria;

        $this->assertCount(11, $criteria->getAllFilters());
        collect($criteria->getAllFilters())
            ->each(fn($i) => $this->assertInstanceOf(AllowedFilter::class, $i));

        $this->assertCount(1, $criteria->getDefaultSorts());
        collect($criteria->getDefaultSorts())
            ->each(fn($i) => $this->assertInstanceOf(AllowedSort::class, $i));
        $this->assertCount(3, $criteria->getSorts());
        collect($criteria->getSorts())
            ->each(fn($i) => $this->assertInstanceOf(AllowedSort::class, $i));

        $this->assertCount(11, $criteria->getAllIncludes());
        collect($criteria->getAllIncludes())
            ->each(fn($i) => $this->assertInstanceOf(AllowedInclude::class, $i));

        $this->assertCount(2, $criteria->getFields());
        collect($criteria->getFields())->each(fn($i) => is_string($i));
        $this->assertCount(2, $criteria->getDefaultFields());
        collect($criteria->getDefaultFields())->each(fn($i) => is_string($i));
    }
}
