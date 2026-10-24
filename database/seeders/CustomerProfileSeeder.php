<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerProfileSeeder extends Seeder
{
    public function run(): void
    {
        User::where('role', 'customer')->whereDoesntHave('customerProfile')->each(function (User $user): void {
            $user->customerProfile()->create([]);
        });
    }
}
