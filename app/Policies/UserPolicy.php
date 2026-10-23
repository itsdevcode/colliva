<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, User $record): bool
    {
        return $user->role === 'admin' || $user->id === $record->id;
    }

    public function update(User $user, User $record): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, User $record): bool
    {
        return false;
    }
}
