<?php

namespace Database\Factories;

use App\Models\Collaboration;
use App\Models\CollaborationDeliverable;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CollaborationDeliverable> */
class CollaborationDeliverableFactory extends Factory
{
    public function definition(): array
    {
        return ['collaboration_id' => Collaboration::factory(), 'notes' => 'First edit ready for review.', 'url' => 'https://example.com/deliverable'];
    }
}
