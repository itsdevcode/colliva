<?php

namespace Database\Factories;

use App\Models\CreatorProfile;
use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SocialAccount> */
class SocialAccountFactory extends Factory
{
    public function definition(): array
    {
        return ['creator_profile_id' => CreatorProfile::factory(), 'platform' => 'instagram', 'url' => 'https://www.instagram.com/example', 'followers' => 1000, 'metrics_reported_at' => now()];
    }
}
