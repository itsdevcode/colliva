<?php

namespace App\Livewire;

use App\Actions\ManageCollaboration;
use App\Models\CollaborationRequest;
use App\Models\CreatorProfile;
use App\Notifications\MarketplaceUpdate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.marketplace')]
class CollaborationWorkspace extends Component
{
    #[Url]
    public string $creator = '';

    public string $title = '';

    public string $scope = '';

    public string $price = '';

    public string $deadline = '';

    /** @var array<int, string> */
    public array $responses = [];

    /** @var array<int, string> */
    public array $urls = [];

    /** @var array<int, string> */
    public array $notes = [];

    /** @var array<int, string> */
    public array $revisedScopes = [];

    /** @var array<int, string> */
    public array $revisedPrices = [];

    /** @var array<int, string> */
    public array $revisedDeadlines = [];

    public function send(): void
    {
        $user = auth()->user();
        abort_unless(($user->role === 'brand' && $user->brandProfile) || ($user->role === 'customer' && $user->customerProfile), 403);
        $this->validate(['creator' => ['required', 'string', 'max:100'], 'title' => ['required', 'string', 'max:150'], 'scope' => ['required', 'string', 'min:20', 'max:10000'], 'price' => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/', 'numeric', 'min:1', 'max:100000000'], 'deadline' => ['required', 'date', 'after:today']]);
        abort_if(RateLimiter::tooManyAttempts('brief:'.$user->id, 10), 429);
        $creator = CreatorProfile::publiclyVisible()->where('slug', $this->creator)->where('available', true)->firstOrFail();
        $offer = CollaborationRequest::create(['brand_profile_id' => $user->role === 'brand' ? $user->brandProfile->id : null, 'customer_profile_id' => $user->role === 'customer' ? $user->customerProfile->id : null, 'creator_profile_id' => $creator->id, 'title' => $this->title, 'scope' => $this->scope, 'price_minor' => (int) round((float) $this->price * 100), 'delivery_deadline' => $this->deadline]);
        $offer->forceFill(['brand_confirmed_at' => now()])->save();
        RateLimiter::hit('brief:'.$user->id, 3600);
        $creator->user->notify(new MarketplaceUpdate('You received a new collaboration brief.'));
        $this->reset('creator', 'title', 'scope', 'price', 'deadline');
        session()->flash('status', 'Brief sent. Payment will be coordinated directly between both parties.');
    }

    public function respond(int $id, string $decision, ManageCollaboration $workflow): void
    {
        $workflow->respond(auth()->user(), $id, $decision, $this->responses[$id] ?? '');
    }

    public function revise(int $id, ManageCollaboration $workflow): void
    {
        $this->validate(["revisedPrices.$id" => ['required', 'numeric', 'min:1', 'max:100000000']]);
        $workflow->revise(auth()->user(), $id, $this->revisedScopes[$id] ?? '', (int) round((float) $this->revisedPrices[$id] * 100), $this->revisedDeadlines[$id] ?? '');
    }

    public function submit(int $id, ManageCollaboration $workflow): void
    {
        $workflow->submit(auth()->user(), $id, $this->urls[$id] ?? '', $this->notes[$id] ?? '');
    }

    public function review(int $id, string $decision, ManageCollaboration $workflow): void
    {
        $workflow->review(auth()->user(), $id, $decision);
    }

    public function markRead(): void
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render(): View
    {
        $user = auth()->user();
        $offers = CollaborationRequest::where(fn ($q) => $q->whereHas('brandProfile', fn ($b) => $b->where('user_id', $user->id))->orWhereHas('customerProfile', fn ($c) => $c->where('user_id', $user->id))->orWhereHas('creatorProfile', fn ($c) => $c->where('user_id', $user->id)))->with(['customerProfile.user', 'brandProfile.user', 'creatorProfile', 'collaboration.deliverables'])->latest('id')->get();

        return view('livewire.collaboration-workspace', ['offers' => $offers, 'notifications' => $user->notifications()->latest()->limit(25)->get()])->title('Collaborations');
    }
}
