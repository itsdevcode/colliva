<?php

namespace Database\Factories;

use App\Models\CreatorProfile;
use App\Models\PortfolioItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PortfolioItem> */
class PortfolioItemFactory extends Factory
{
    public function definition(): array
    {
        return ['creator_profile_id' => CreatorProfile::factory(), 'title' => fake()->sentence(3), 'description' => fake()->paragraph(), 'image_path' => 'portfolio/test.jpg', 'rights_confirmed_at' => now()];
    }
}
