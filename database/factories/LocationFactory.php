<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Location> */
class LocationFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->city(), 'slug' => fake()->unique()->slug(), 'state' => 'Haryana', 'country' => 'IN'];
    }
}
