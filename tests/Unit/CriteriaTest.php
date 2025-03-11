<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Omaressaouaf\QueryBuilderCriteria\TestCriteria;
use Omaressaouaf\QueryBuilderCriteria\Tests\TestCase;

class CriteriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_works(): void
    {
        $criteria = new TestCriteria();
    }
}
