<?php

namespace Database\Factories;

use App\Models\PlatformSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PlatformSetting> */
class PlatformSettingFactory extends Factory
{
    public function definition(): array
    {
        return ['key' => fake()->unique()->slug(), 'value' => fake()->sentence()];
    }
}
