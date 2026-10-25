<?php

namespace Database\Factories;

use App\Models\SupportArticle;
use App\Models\SupportCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SupportArticle> */
class SupportArticleFactory extends Factory
{
    public function definition(): array
    {
        return ['support_category_id' => SupportCategory::factory(), 'title' => fake()->sentence(), 'slug' => fake()->unique()->slug(), 'summary' => fake()->sentence(), 'content' => fake()->paragraph(), 'status' => 'published', 'published_at' => now(), 'sort_order' => 0];
    }
}
