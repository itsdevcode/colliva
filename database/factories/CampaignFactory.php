<?php

namespace Database\Factories;

use App\Models\BrandProfile;
use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Campaign> */
class CampaignFactory extends Factory
{
    public function definition(): array
    {
        return ['brand_profile_id' => BrandProfile::factory(), 'category_id' => Category::factory(), 'title' => fake()->sentence(4), 'description' => fake()->paragraph(), 'deliverables' => 'One original reel', 'requirements' => 'Local food creator', 'budget_minor' => 1000000, 'application_deadline' => today()->addDays(7), 'delivery_deadline' => today()->addDays(14), 'status' => 'draft'];
    }
}
