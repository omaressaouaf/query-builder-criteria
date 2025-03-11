<?php

namespace Omaressaouaf\QueryBuilderCriteria;

class TestCriteria extends Criteria
{
    protected array $searches = [
        'first_name',
        'last_name',
        'posts.title',
        'posts.body',
        'posts.comments.user_reaction',
        'posts.comments.watchers.id'
    ];

    protected array $fullTextSearches = [
        'full_first_name',
        'full_last_name',
        'full_posts.full_title',
        'full_posts.full_body',
        'full_posts.comments.full_user_reaction',
        'full_posts.comments.full_watchers.id',
        'full_posts.comments.full_watchers.user_name',
    ];
}
