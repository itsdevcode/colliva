<?php

namespace Database\Factories;

use App\Models\ContentReport;
use App\Models\CreatorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContentReport> */
class ContentReportFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'creator_profile_id' => CreatorProfile::factory(), 'reason' => 'Please review ownership of this image.'];
    }
}
