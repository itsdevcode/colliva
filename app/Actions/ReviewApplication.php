<?php

namespace App\Actions;

use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\CollaborationRequest;
use App\Models\User;
use App\Notifications\MarketplaceUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ReviewApplication
{
    public function handle(User $brand, int $applicationId, string $decision): void
    {
        DB::transaction(function () use ($brand, $applicationId, $decision) {
            $application = CampaignApplication::lockForUpdate()->findOrFail($applicationId);
            $campaign = Campaign::lockForUpdate()->findOrFail($application->campaign_id);
            Gate::forUser($brand)->authorize('update', $campaign);
            if (! in_array($decision, ['shortlisted', 'accepted', 'rejected'], true) || ! in_array($application->status, ['submitted', 'shortlisted'], true) || $campaign->status !== 'published') {
                throw ValidationException::withMessages(['application' => 'This application cannot be reviewed in its current state.']);
            }
            $application->forceFill(['status' => $decision])->save();
            if ($decision === 'accepted') {
                $offer = CollaborationRequest::create(['brand_profile_id' => $campaign->brand_profile_id, 'creator_profile_id' => $application->creator_profile_id, 'campaign_application_id' => $application->id, 'title' => $campaign->title, 'scope' => $campaign->deliverables."\n".$application->proposal, 'price_minor' => $application->quote_minor, 'delivery_deadline' => $campaign->delivery_deadline]);
                $offer->forceFill(['brand_confirmed_at' => now()])->save();
            }
            $application->creatorProfile->user->notify(new MarketplaceUpdate('Your application to '.$campaign->title.' was '.$decision.'.'));
        });
    }
}
