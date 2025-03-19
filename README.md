
# Query Builder Criteria

[![Latest Stable Version](https://img.shields.io/packagist/v/omaressaouaf/query-builder-criteria.svg)](https://packagist.org/packages/omaressaouaf/query-builder-criteria)
[![License](https://img.shields.io/github/license/omaressaouaf/query-builder-criteria)](LICENSE)
[![Tests](https://github.com/omaressaouaf/query-builder-criteria/actions/workflows/tests.yml/badge.svg)](https://github.com/omaressaouaf/query-builder-criteria/actions/workflows/tests.yml)


### **Introduction:**

**Query Builder Criteria** is a Laravel package that extends [Spatie's Laravel Query Builder](https://spatie.be/docs/laravel-query-builder/v6/introduction), providing a structured way to define query filters, sorting, includes, and search functionality using reusable **criteria classes**.

With this package, you can:

- ✅ Define query logic in a **clean, structured** manner
- ✅ Apply **filters, sorts, includes, and field selections** effortlessly
- ✅ Support **search** and **full-text search** and **custom query aliases**
- ✅ **Merge multiple criteria dynamically** for flexible querying

Built on top of Spatie’s Query Builder, this package **removes repetitive query logic**, keeping your controllers and models **clean and maintainable**. 🚀

**🔗 Example:**

Instead of manually handling query parameters, just define a **Criteria Class**:

```php
class PostCriteria extends Criteria
{
    protected array $filters = ['title', 'slug', 'user'];

    protected array $sorts = ['published_at', 'created_at'];

    protected array $includes = ['user'];
}
```

And apply it with **one line of code**:

```php
$posts = Post::query()->queryByCriteria(PostCriteria::class)->get();
```

✨ No more manual query handling – just **define once and reuse**!

## Table of Contents

- [Installation](#installation)
- [Get Started](#get-started)
- [Criteria configuration](#criteria-configuration)
  - [Filters](#filters)
  - [Sorting](#sorting)
  - [Includes](#includes)
  - [Field Selection](#field-selection)
  - [Search](#search)
  - [Aliases](#aliases)
  - [Advanced Features](#advanced-features)
- [Applying Criteria to a Model](#applying-criteria-to-a-model)
- [Querying Data](#querying-data)
- [Passing Criteria to Scope](#passing-criteria-to-scope)
- [Credits](#credits)
- [License](#license)

## Installation {#installation}

Install via Composer:

```bash
composer require omaressaouaf/query-builder-criteria
```

### **Publishing the Configuration**

After installation, you can publish the package configuration file using:

```bash
php artisan vendor:publish --provider="Omaressaouaf\QueryBuilderCriteria\QueryBuilderCriteriaServiceProvider" --tag=query-builder-criteria-config
```

This will create a configuration file in `config/query-builder-criteria.php`, allowing you to customize the default behavior.

```php
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

```

---

## Get Started {#get-started}

This package allows you to define query criteria for your models, enabling filtering, sorting, field selection, and more.

### Example: Post Criteria

Let's say we have a `Post` model. We want to allow users to:
- Filter by `title`, `id`, and `slug`.
- Include related `user` model.
- Sort by `published_at`.
- Select specific fields like `title` and `body`.

We define these rules in a **criteria class**:

```php
namespace App\Criteria;

use Omaressaouaf\QueryBuilderCriteria\Criteria as BaseCriteria;

class PostCriteria extends BaseCriteria
{
    protected array $filters = ['title'];

    protected array $exactFilters = ['id', 'slug'];

    protected array $sorts = ['published_at'];

    protected array $includes = ['user'];

    protected array $fields = ['id', 'title', 'body', 'published_at', 'user_id'];
}
```

Now, in our `Post` model:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Omaressaouaf\QueryBuilderCriteria\QueryableByCriteria;
use Omaressaouaf\QueryBuilderCriteria\Tests\Criteria\PostCriteria;

class Post extends Model
{
    use QueryableByCriteria;

    protected function defaultQueryBuilderCriteria(): array|string
    {
        return PostCriteria::class;
    }
}
```

Then we can call the scope

```php
Post::query()->queryByCriteria()->get();
```

Now we can query posts like this:

```php
GET /posts?filter[title]=Hello&include=user&sort=-published_at&fields[posts]=id,title
```

---

## Criteria configuration {#criteria-configuration}

### Filters {#filters}

#### URL Query Example:

```
GET /posts?filter[title]=Hello World&filter[id]=1&filter[user]=5&filter[trashed]=with
```

#### Configuration:

```php
protected array $filters = ['title'];

protected array $exactFilters = ['id', 'slug'];

protected array $belongsToFilters = ['user'];

protected array $scopeFilters = ['published_before'];

protected string|bool|array|null $trashedFilter = true;
```

- **filters**: Allows filtering by `title`.
- **exactFilters**: Enables exact filtering on `id` and `slug`.
- **belongsToFilters**: Supports filtering by related `user` model.
- **scopeFilters**: Uses model scopes like `published_before`.
- **trashedFilter**: Enables soft-deleted filter. (Possible values : `with`, `only`)

### Sorting {#sorting}

#### URL Query Example:

```
GET /posts?sort=-published_at
```

#### Configuration:

```php
protected array|string $defaultSorts = '-created_at';

protected array $sorts = ['published_at', 'created_at'];
```

- **defaultSorts**: Defaults to sorting by `created_at` in descending order.
- **sorts**: Allows sorting by `published_at` and `created_at`.

### Includes {#includes}

#### URL Query Example:

```
GET /posts?include=user,commentsCount,commentsExists
```

#### Configuration:

```php
protected array $includes = ['user'];

protected array $countIncludes = ['comments'];

protected array $existsIncludes = ['comments'];
```

- **includes**: Enables including related models (`user`).
- **countIncludes**: Enables including only count of related records (`comments_count`).
- **existsIncludes**: Enables including only whether related models exist (`has_comments`).

### Field Selection {#field-selection}

#### URL Query Example:

```
GET /posts?fields[posts]=id,slug,title,body,published_at
```

#### Configuration:

```php
protected array $defaultFields = ['id', 'slug', 'title'];

protected array $fields = ['id', 'slug', 'title', 'body', 'published_at', 'user_id', 'created_at', 'updated_at'];
```

- **defaultFields**: Default fields returned in queries when nothing specified in query param.
- **fields**: Lists all fields that can be selected.

### Search {#search}

#### URL Query Example:

```
GET /posts?filter[search_query]=hello world
```

#### Configuration:

```php
protected array $searches = ['slug', 'title', 'user.name'];

protected array $fullTextSearches = ['body', 'user.bio'];

protected ?bool $splitSearchIntoTerms = false;
```

- **searches**: Supports searching in `slug`, `title`, and `user.name`.
- **fullTextSearches**: Full-text search columns `body` and `user.bio`.
- **splitSearchIntoTerms**: Enable or Disable term splitting in searches.

### Aliases {#aliases}

You can define an alias for a filter to keep your database structure hidden and make URLs more readable. For example, if your users table has a `user_passport_full_name` column, exposing it directly in the API isn't ideal. Instead, you can assign a more user-friendly alias:

#### URL Query Example:

```
GET /posts?filter[name]=John
```

#### Configuration:

```php
protected array $filters = [
    'name' => 'user_passport_full_name'
];
```

This pattern is applicable for all filters, sorts and includes

## Advanced Features {#advanced-features}

### Advanced Filters, Sorting, and Includes

For more complex filters, sorts, and includes like specifying ignored and default values, custom callback filters and more you can override the following methods in your `Criteria` class, these will be merged with the declared properties

#### Example Configuration

```php
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\AllowedInclude;

protected function advancedFilters(): array
{
    return [
        AllowedFilter::callback('cb_filter_1', fn () => ''),
    ];
}

protected function advancedDefaultSorts(): array
{
    return [
        AllowedSort::callback('cb_sort_1', fn () => ''),
    ];
}

protected function advancedSorts(): array
{
    return [
        AllowedSort::callback('cb_sort_1', fn () => ''),
    ];
}

protected function advancedIncludes(): array
{
    return [
        AllowedInclude::callback('cb_include', fn () => ''),
    ];
}
```

Refer to the [Spatie Query Builder Documentation](https://spatie.be/docs/laravel-query-builder/v6/features/filtering) for more details on defining advanced filters, sorts, and includes.

---

## Applying Criteria to a Model {#applying-criteria-to-a-model}

Use the `QueryableByCriteria` trait in your model:

```php
namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Omaressaouaf\QueryBuilderCriteria\QueryableByCriteria;
use App\Criteria\PostCriteria;

class Post extends Model
{
    use QueryableByCriteria;

    protected function defaultQueryBuilderCriteria(): array|string
    {
        return PostCriteria::class;
    }
}
```

## Querying Data {#querying-data}

```php
return Post::query()->queryByCriteria()->get();
```

## Passing Criteria to Scope {#passing-criteria-to-scope}

You can also pass a criteria class directly to the `queryByCriteria` scope. This allows you to merge additional criteria with the default criteria for flexibility and reusability:

```php
return Post::query()->queryByCriteria(CustomCriteria::class, AnotherCriteria::class)->get();
```

---

## Credits {#credits}

This package is based on [Spatie's Laravel Query Builder](https://spatie.be/docs/laravel-query-builder/v6/introduction).

## License {#license}

This package is open-source and licensed under the [MIT License](https://github.com/omaressaouaf/query-builder-criteria/blob/master/LICENSE).
