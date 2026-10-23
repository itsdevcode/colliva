<?php

namespace Database\Factories;

use App\Models\BrandProfile;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BrandProfile> */
class BrandProfileFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory()->state(['role' => 'brand']), 'location_id' => Location::factory(), 'company_name' => fake()->company(), 'description' => fake()->paragraph()];
    }
}
