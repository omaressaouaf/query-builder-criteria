<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Omaressaouaf\QueryBuilderCriteria\Tests\Factories\PostFactory;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return PostFactory::new();
    }
}
