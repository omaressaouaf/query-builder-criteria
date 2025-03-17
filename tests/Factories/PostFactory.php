<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Omaressaouaf\QueryBuilderCriteria\Tests\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->slug(3),
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(4),
            'published_at' => $this->faker->dateTime(),
            'user_id' => UserFactory::new(),
        ];
    }
}
