<?php

namespace Database\Factories;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SupportMessage> */
class SupportMessageFactory extends Factory
{
    public function definition(): array
    {
        return ['support_ticket_id' => SupportTicket::factory(), 'user_id' => User::factory(), 'body' => fake()->paragraph(), 'internal' => false, 'staff' => false];
    }
}
