<?php
namespace App\Policies;
use App\Models\{Collaboration, User};
class CollaborationPolicy
{
 public function view(User $user, Collaboration $collaboration): bool { return in_array($user->id, [$collaboration->request->brandProfile->user_id, $collaboration->request->creatorProfile->user_id], true); }
 public function submit(User $user, Collaboration $collaboration): bool { return $user->role === 'creator' && $collaboration->request->creatorProfile->user_id === $user->id; }
 public function review(User $user, Collaboration $collaboration): bool { return $user->role === 'brand' && $collaboration->request->brandProfile->user_id === $user->id; }
 public function viewAny(User $user): bool { return $user->role === 'admin'; }
}
