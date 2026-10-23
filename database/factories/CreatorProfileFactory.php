<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Location;
use App\Models\PortfolioItem;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CreatorProfile> */
class CreatorProfileFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'location_id' => Location::factory(), 'category_id' => Category::factory(), 'display_name' => fake()->name(), 'slug' => fake()->unique()->slug(), 'bio' => fake()->paragraph(), 'photo_path' => 'creator-photos/test.jpg', 'status' => 'draft', 'available' => true, 'is_demo' => false];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'approved', 'publication_consented_at' => now()])->has(PortfolioItem::factory())->has(ServicePackage::factory());
    }
}
