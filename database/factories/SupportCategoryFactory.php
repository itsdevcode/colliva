<?php

namespace Database\Factories;

use App\Models\SupportCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SupportCategory> */
class SupportCategoryFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->words(2, true), 'slug' => fake()->unique()->slug(), 'description' => fake()->sentence(), 'sort_order' => 0];
    }
}
