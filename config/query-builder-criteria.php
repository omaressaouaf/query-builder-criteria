<?php

return [
    /**
     * The name of the search_query_parameter inside the query string
     * For example: GET /users?search_query=john
     */
    'search_query_parameter' => 'search_query',

    /**
     * Split the search query into an array of terms to compare to
     * This is applied for all criteria unless overridden inside the criteria class
     */
    'split_search_into_terms' => false,

    /**
     * Define a default sorts for all criteria
     * This is applied for all criteria unless overridden inside the criteria class
     *
     * Possible value types: array, string, null
     */
    'default_sorts' => null,
];
