<?php

namespace Omaressaouaf\QueryBuilderCriteria\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Omaressaouaf\QueryBuilderCriteria\Tests\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->email(),
            'bio' => $this->faker->paragraph(4),
        ];
    }
}
