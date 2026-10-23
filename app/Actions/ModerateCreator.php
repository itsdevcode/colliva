<?php

namespace App\Actions;

use App\Models\AuditLog;
use App\Models\CreatorProfile;
use App\Models\User;
use App\Notifications\MarketplaceUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ModerateCreator
{
    public function handle(User $admin, CreatorProfile $profile, string $status, ?string $note = null): void
    {
        Gate::forUser($admin)->authorize('moderate', $profile);
        DB::transaction(function () use ($admin, $profile, $status, $note) {
            $profile = CreatorProfile::lockForUpdate()->findOrFail($profile->id);
            if (! in_array($status, ['approved', 'rejected'], true) || $profile->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Only pending profiles can be reviewed.']);
            }
            if ($status === 'approved' && (! $profile->publication_consented_at || ! $profile->user->hasVerifiedEmail() || ! $profile->location_id || ! $profile->category_id || ! $profile->photo_path || ! $profile->servicePackages()->exists() || ! $profile->portfolioItems()->where('hidden', false)->exists())) {
                throw ValidationException::withMessages(['status' => 'Complete the profile, verify email and obtain publication consent before approval.']);
            }
            $profile->forceFill(['status' => $status, 'moderation_note' => $note])->save();
            AuditLog::create(['user_id' => $admin->id, 'action' => 'creator.'.$status, 'subject_type' => CreatorProfile::class, 'subject_id' => $profile->id, 'note' => $note]);
            $profile->user->notify(new MarketplaceUpdate('Your creator profile was '.$status.'.'.($note ? ' '.$note : '')));
        });
    }
}
