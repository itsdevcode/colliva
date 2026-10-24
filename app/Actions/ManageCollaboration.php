<?php

namespace App\Actions;

use App\Models\Collaboration;
use App\Models\CollaborationRequest;
use App\Models\User;
use App\Notifications\MarketplaceUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ManageCollaboration
{
    public function respond(User $user, int $requestId, string $decision, string $response = ''): void
    {
        DB::transaction(function () use ($user, $requestId, $decision, $response) {
            $offer = CollaborationRequest::lockForUpdate()->findOrFail($requestId);
            $isCreator = $offer->creatorProfile->user_id === $user->id && $user->role === 'creator';
            $isBrand = $offer->hirer()->id === $user->id && in_array($user->role, ['brand', 'customer'], true);
            abort_unless($isCreator || $isBrand, 404);
            if ($isBrand && $decision === 'cancelled' && in_array($offer->status, ['pending', 'changes_requested'], true)) {
                $offer->forceFill(['status' => 'cancelled'])->save();

                return;
            }
            if (! $isCreator || ! $offer->brand_confirmed_at || ! in_array($decision, ['accepted', 'rejected', 'changes_requested'], true) || $offer->status !== 'pending') {
                throw ValidationException::withMessages(['offer' => 'This request cannot make that transition.']);
            }
            Validator::make(['response' => $response], ['response' => [$decision === 'changes_requested' ? 'required' : 'nullable', 'string', 'max:2000']])->validate();
            $offer->forceFill(['status' => $decision, 'creator_response' => $response, 'creator_confirmed_at' => $decision === 'accepted' ? now() : null])->save();
            if ($decision === 'accepted') {
                $offer->collaboration()->create([]);
            }
            $offer->hirer()->notify(new MarketplaceUpdate('Your brief “'.$offer->title.'” was '.$decision.'.'));
        });
    }

    public function revise(User $user, int $requestId, string $scope, int $priceMinor, string $deadline): void
    {
        DB::transaction(function () use ($user, $requestId, $scope, $priceMinor, $deadline) {
            $offer = CollaborationRequest::lockForUpdate()->findOrFail($requestId);
            abort_unless(in_array($user->role, ['brand', 'customer'], true) && $offer->hirer()->id === $user->id, 404);
            if ($offer->status !== 'changes_requested') {
                throw ValidationException::withMessages(['offer' => 'Only a request awaiting changes can be revised.']);
            }
            Validator::make(['scope' => $scope, 'price' => $priceMinor, 'deadline' => $deadline], ['scope' => ['required', 'string', 'max:10000'], 'price' => ['integer', 'min:100', 'max:10000000000'], 'deadline' => ['date', 'after_or_equal:today']])->validate();
            $offer->forceFill(['scope' => $scope, 'price_minor' => $priceMinor, 'delivery_deadline' => $deadline, 'status' => 'pending', 'brand_confirmed_at' => now(), 'creator_confirmed_at' => null])->save();
            $offer->creatorProfile->user->notify(new MarketplaceUpdate('A revised brief is ready for your review.'));
        });
    }

    public function submit(User $user, int $collaborationId, string $url, string $notes): void
    {
        DB::transaction(function () use ($user, $collaborationId, $url, $notes) {
            $collaboration = Collaboration::lockForUpdate()->findOrFail($collaborationId);
            Gate::forUser($user)->authorize('submit', $collaboration);
            if (! in_array($collaboration->status, ['active', 'revision_requested'], true)) {
                throw ValidationException::withMessages(['collaboration' => 'Deliverables can only be submitted to an active collaboration.']);
            }
            Validator::make(compact('url', 'notes'), ['url' => ['required', 'url:https', 'max:255'], 'notes' => ['required', 'string', 'max:5000']])->validate();
            $collaboration->deliverables()->create(compact('url', 'notes'));
            $collaboration->forceFill(['status' => 'submitted'])->save();
            $collaboration->request->hirer()->notify(new MarketplaceUpdate('Deliverables are ready to review.'));
        });
    }

    public function review(User $user, int $collaborationId, string $decision): void
    {
        DB::transaction(function () use ($user, $collaborationId, $decision) {
            $collaboration = Collaboration::lockForUpdate()->findOrFail($collaborationId);
            Gate::forUser($user)->authorize('review', $collaboration);
            if ($collaboration->status !== 'submitted' || ! in_array($decision, ['completed', 'revision_requested'], true)) {
                throw ValidationException::withMessages(['collaboration' => 'Only submitted work can be reviewed.']);
            }
            $collaboration->forceFill(['status' => $decision, 'completed_at' => $decision === 'completed' ? now() : null])->save();
            $collaboration->request->creatorProfile->user->notify(new MarketplaceUpdate('Your collaboration was marked '.$decision.'.'));
        });
    }
}
