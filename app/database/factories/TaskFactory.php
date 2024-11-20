<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(rand(2, 3), true),
            'description' => fake()->sentences(rand(0, 3), true),
            'finished_at' => rand(0, 5) === 0 ? null : fake()->dateTimeThisYear(),
        ];
    }
}
