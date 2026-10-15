<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Ad>
 */
class AdFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(5, 5000) * 1000,
            'location' => fake()->randomElement(config('akwaba.locations')),
            'condition' => fake()->randomElement(array_keys(config('akwaba.conditions'))),
        ];
    }
}
