<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'role' => ['sometimes', 'required', 'in:creator,brand,customer'],
            'city' => ['required_if:role,creator,brand', 'nullable', 'exists:locations,id'],
            'category' => ['required_if:role,creator', 'nullable', 'exists:categories,id'],
            'company_name' => ['required_if:role,brand', 'nullable', 'string', 'max:150'],
            'business_category' => ['required_if:role,brand', 'nullable', 'string', 'max:150'],
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $user = new User([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);
            $user->role = match ($input['role'] ?? 'creator') {
                'brand' => 'brand',
                'customer' => 'customer',
                default => 'creator',
            };
            $user->save();

            if ($user->role === 'creator') {
                $profileSlug = trim(Str::substr(Str::slug($user->name), 0, 80), '-') ?: 'creator';
                $user->creatorProfile()->create(['display_name' => $user->name, 'slug' => $profileSlug.'-'.Str::lower(Str::random(10)), 'location_id' => $input['city'] ?? null, 'category_id' => $input['category'] ?? null]);
            } elseif ($user->role === 'brand') {
                $user->brandProfile()->create(['company_name' => $input['company_name'], 'business_category' => $input['business_category'], 'location_id' => $input['city']]);
            } else {
                $user->customerProfile()->create([]);
            }

            return $user;
        });
    }
}
