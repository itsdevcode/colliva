<?php

namespace Database\Factories;

use App\Models\CreatorProfile;
use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ServicePackage> */
class ServicePackageFactory extends Factory
{
    public function definition(): array
    {
        return ['creator_profile_id' => CreatorProfile::factory(), 'name' => 'Instagram Reel', 'description' => 'An original vertical video.', 'price_minor' => 250000, 'deliverables' => 'One edited 30-second reel', 'turnaround_days' => 7, 'revision_policy' => 'One revision included'];
    }
}
