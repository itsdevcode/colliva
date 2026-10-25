<?php

namespace App\Policies;

use App\Models\SupportArticle;
use App\Models\User;

class SupportArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' && $user->hasVerifiedEmail();
    }

    public function view(User $user, SupportArticle $record): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, SupportArticle $record): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, SupportArticle $record): bool
    {
        return false;
    }
}
