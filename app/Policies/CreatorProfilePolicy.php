<?php
namespace App\Policies;
use App\Models\{CreatorProfile, User};
class CreatorProfilePolicy
{
 public function create(User $user): bool { return $user->role === 'creator'; }
 public function update(User $user, CreatorProfile $profile): bool { return $user->role === 'creator' && $profile->user_id === $user->id; }
 public function moderate(User $user, CreatorProfile $profile): bool { return $user->role === 'admin' && $user->hasVerifiedEmail(); }
 public function viewAny(User $user): bool { return $user->role === 'admin'; }
 public function view(User $user, CreatorProfile $profile): bool { return $user->role === 'admin' || $profile->user_id === $user->id; }
}
