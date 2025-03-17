<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Omaressaouaf\QueryBuilderCriteria\QueryableByCriteria;
use Omaressaouaf\QueryBuilderCriteria\Tests\Criteria\PostCriteria;
use Omaressaouaf\QueryBuilderCriteria\Tests\Factories\PostFactory;

class Post extends Model
{
    use HasFactory, SoftDeletes, QueryableByCriteria;

    protected $guarded = [];

    protected $casts = [
        'published_before' => 'dateTime'
    ];

    protected static function newFactory()
    {
        return PostFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublishedBefore(Builder $query, $date): Builder
    {
        return $query->where('published_at', '<=', $date);
    }

    protected function defaultQueryBuilderCriteria(): array|string
    {
        return PostCriteria::class;
    }
}
