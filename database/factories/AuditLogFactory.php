<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\CreatorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AuditLog> */
class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory()->state(['role' => 'admin']), 'action' => 'creator.approved', 'subject_type' => CreatorProfile::class, 'subject_id' => 1];
    }
}
