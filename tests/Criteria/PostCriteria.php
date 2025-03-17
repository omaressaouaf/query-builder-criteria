<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Criteria;

use Omaressaouaf\QueryBuilderCriteria\Criteria as BaseCriteria;

class PostCriteria extends BaseCriteria
{
    protected array $filters = ['title'];

    protected array $exactFilters = ['id', 'slug'];

    protected array $belongsToFilters = ['user'];

    protected array $scopeFilters = ["published_before"];

    protected string|bool|array|null $trashedFilter = true;

    protected array|string $defaultSorts = '-published_at';

    protected array $sorts = ['published_at', 'created_at'];

    protected array $includes = ['user'];

    protected array $defaultFields = ['id', 'slug', 'title', 'body', 'published_at', 'user_id'];

    protected array $fields = ['id', 'slug', 'title', 'body', 'published_at', 'user_id', 'created_at', 'updated_at'];

    protected array $searches = ['slug', 'title', 'user.name'];

    protected array $fullTextSearches = ['body', 'user.bio'];

    protected ?bool $splitSearchIntoTerms = false;
}
