<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Omaressaouaf\QueryBuilderCriteria\Tests\Factories\PostFactory;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function newFactory()
    {
        return PostFactory::new();
    }
}
