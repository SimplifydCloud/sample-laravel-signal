<?php

namespace Database\Factories;

use App\Models\Idea;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Idea> */
class IdeaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'category' => 'Experience',
            'status' => 'Exploring',
            'votes' => 0,
        ];
    }
}
