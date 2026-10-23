<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\CreatorProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CampaignApplication> */
class CampaignApplicationFactory extends Factory
{
    public function definition(): array
    {
        return ['campaign_id' => Campaign::factory(), 'creator_profile_id' => CreatorProfile::factory(), 'proposal' => 'I would love to create a local story for your brand.', 'quote_minor' => 250000];
    }
}
