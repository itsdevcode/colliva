<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CampaignPlatform> */
class CampaignPlatformFactory extends Factory
{
    public function definition(): array
    {
        return ['campaign_id' => Campaign::factory(), 'platform' => 'instagram'];
    }
}
