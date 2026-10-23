<?php

namespace Database\Factories;

use App\Models\Collaboration;
use App\Models\CollaborationRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Collaboration> */
class CollaborationFactory extends Factory
{
    public function definition(): array
    {
        return ['collaboration_request_id' => CollaborationRequest::factory()->state(['status' => 'accepted', 'creator_confirmed_at' => now()])];
    }
}
