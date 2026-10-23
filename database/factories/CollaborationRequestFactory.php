<?php

namespace Database\Factories;

use App\Models\BrandProfile;
use App\Models\CollaborationRequest;
use App\Models\CreatorProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CollaborationRequest> */
class CollaborationRequestFactory extends Factory
{
    public function definition(): array
    {
        return ['brand_profile_id' => BrandProfile::factory(), 'creator_profile_id' => CreatorProfile::factory(), 'title' => 'Original product story', 'scope' => 'One 30-second reel with one revision. Direct payment on delivery.', 'price_minor' => 250000, 'delivery_deadline' => today()->addDays(10), 'brand_confirmed_at' => now()];
    }
}
