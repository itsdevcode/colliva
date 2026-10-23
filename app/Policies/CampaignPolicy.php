<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'brand' && $user->hasVerifiedEmail();
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $user->role === 'brand' && $campaign->brandProfile->user_id === $user->id;
    }

    public function apply(User $user, Campaign $campaign): bool
    {
        $profile = $user->creatorProfile;

        return $user->role === 'creator' && $user->hasVerifiedEmail() && $profile && $profile->status === 'approved' && $profile->publication_consented_at && $profile->available && $campaign->status === 'published' && $campaign->application_deadline->endOfDay()->isFuture() && $campaign->locations()->whereKey($profile->location_id)->exists() && ($campaign->category_id === $profile->category_id || $profile->categories()->whereKey($campaign->category_id)->exists()) && ! $campaign->platforms()->whereNotIn('platform', $profile->socialAccounts()->pluck('platform'))->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Campaign $campaign): bool
    {
        return $user->role === 'admin' || $campaign->brandProfile->user_id === $user->id;
    }
}
