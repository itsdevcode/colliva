<?php

namespace App\Policies;

use App\Models\SupportCategory;
use App\Models\User;

class SupportCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' && $user->hasVerifiedEmail();
    }

    public function view(User $user, SupportCategory $record): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, SupportCategory $record): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, SupportCategory $record): bool
    {
        return false;
    }
}
