<?php

namespace App\Livewire;

use App\Actions\ReviewApplication;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Category;
use App\Models\Location;
use App\Notifications\MarketplaceUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.marketplace')]
class CampaignBoard extends Component
{
    use WithPagination;

    public string $title = '';

    public string $description = '';

    public string $category = '';

    /** @var array<int, int|string> */
    public array $locations = [];

    /** @var list<string> */
    public array $platforms = [];

    public string $deliverables = '';

    public string $requirements = '';

    public string $budget = '';

    public string $applicationDeadline = '';

    public string $deliveryDeadline = '';

    /** @var array<int, string> */
    public array $proposals = [];

    /** @var array<int, string> */
    public array $quotes = [];

    public function mount(): void
    {
        abort_unless(in_array(auth()->user()->role, ['brand', 'creator'], true), 403);
    }

    public function createCampaign(bool $publish = false): void
    {
        Gate::authorize('create', Campaign::class);
        $brand = auth()->user()->brandProfile;
        abort_unless($brand !== null, 403);
        $this->validate(['title' => ['required', 'string', 'max:150'], 'description' => ['required', 'string', 'max:10000'], 'category' => ['required', 'exists:categories,id'], 'locations' => ['required', 'array', 'min:1', 'max:30'], 'locations.*' => ['integer', 'exists:locations,id'], 'platforms' => ['required', 'array', 'min:1', 'max:4'], 'platforms.*' => ['in:instagram,youtube,ugc,other'], 'deliverables' => ['required', 'string', 'max:5000'], 'requirements' => ['required', 'string', 'max:5000'], 'budget' => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/', 'numeric', 'min:1', 'max:100000000'], 'applicationDeadline' => ['required', 'date', 'after_or_equal:today'], 'deliveryDeadline' => ['required', 'date', 'after_or_equal:applicationDeadline']]);
        DB::transaction(function () use ($brand, $publish) {
            $campaign = $brand->campaigns()->create(['category_id' => $this->category, 'title' => $this->title, 'description' => $this->description, 'deliverables' => $this->deliverables, 'requirements' => $this->requirements, 'budget_minor' => (int) round((float) $this->budget * 100), 'application_deadline' => $this->applicationDeadline, 'delivery_deadline' => $this->deliveryDeadline]);
            $campaign->locations()->sync($this->locations);
            foreach (array_unique($this->platforms) as $platform) {
                $campaign->platforms()->create(['platform' => $platform]);
            } if ($publish) {
                $campaign->forceFill(['status' => 'pending'])->save();
            }
        });
        $this->reset('title', 'description', 'category', 'locations', 'platforms', 'deliverables', 'requirements', 'budget', 'applicationDeadline', 'deliveryDeadline');
        session()->flash('status', $publish ? 'Campaign sent for moderation.' : 'Campaign draft saved.');
    }

    public function changeStatus(int $id, string $status): void
    {
        DB::transaction(function () use ($id, $status) {
            $campaign = Campaign::lockForUpdate()->findOrFail($id);
            Gate::authorize('update', $campaign);
            $allowed = ['draft' => ['pending', 'cancelled'], 'published' => ['closed', 'cancelled'], 'closed' => ['completed', 'cancelled']];
            if (! in_array($status, $allowed[$campaign->status] ?? [], true)) {
                throw ValidationException::withMessages(['campaign' => 'Invalid campaign status transition.']);
            }
            if ($status === 'pending' && $campaign->application_deadline->endOfDay()->isPast()) {
                throw ValidationException::withMessages(['campaign' => 'The application deadline has passed.']);
            }
            if ($status === 'completed') {
                $accepted = $campaign->applications()->where('status', 'accepted');
                if (! $accepted->exists() || $accepted->whereDoesntHave('collaborationRequest.collaboration', fn ($q) => $q->where('status', 'completed'))->exists()) {
                    throw ValidationException::withMessages(['campaign' => 'Complete all accepted collaborations before completing this campaign.']);
                }
            }
            $campaign->forceFill(['status' => $status])->save();
        });
    }

    public function apply(int $id): void
    {
        $this->validate(["proposals.$id" => ['required', 'string', 'min:20', 'max:3000'], "quotes.$id" => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/', 'numeric', 'min:1', 'max:100000000']]);
        DB::transaction(function () use ($id) {
            $campaign = Campaign::lockForUpdate()->findOrFail($id);
            Gate::authorize('apply', $campaign);
            $profile = auth()->user()->creatorProfile;
            if ($campaign->applications()->where('creator_profile_id', $profile->id)->exists()) {
                throw ValidationException::withMessages(["proposals.$id" => 'You have already applied to this campaign.']);
            }
            $campaign->applications()->create(['creator_profile_id' => $profile->id, 'proposal' => $this->proposals[$id], 'quote_minor' => (int) round((float) $this->quotes[$id] * 100)]);
            $campaign->brandProfile->user->notify(new MarketplaceUpdate('A creator applied to '.$campaign->title.'.'));
        });
        session()->flash('status', 'Application submitted.');
    }

    public function withdraw(int $id): void
    {
        DB::transaction(function () use ($id) {
            $application = CampaignApplication::lockForUpdate()->findOrFail($id);
            abort_unless($application->creatorProfile->user_id === auth()->id(), 404);
            if (! in_array($application->status, ['submitted', 'shortlisted'], true)) {
                throw ValidationException::withMessages(['application' => 'This application can no longer be withdrawn.']);
            } $application->forceFill(['status' => 'withdrawn'])->save();
        });
    }

    public function review(int $id, string $decision, ReviewApplication $review): void
    {
        $review->handle(auth()->user(), $id, $decision);
        session()->flash('status', 'Application updated. Accepted creators must confirm the brief before work begins.');
    }

    public function render(): View
    {
        $user = auth()->user();
        $query = Campaign::with(['brandProfile', 'category', 'locations', 'platforms', 'applications' => fn ($q) => $q->when($user->role === 'creator', fn ($a) => $a->whereHas('creatorProfile', fn ($c) => $c->where('user_id', $user->id)))->with('creatorProfile')]);
        if ($user->role === 'brand') {
            $query->whereHas('brandProfile', fn ($q) => $q->where('user_id', $user->id));
        } else {
            $query->where('status', 'published')->whereDate('application_deadline', '>=', today());
        }

        return view('livewire.campaign-board', ['campaigns' => $query->latest('id')->paginate(10), 'categories' => Category::orderBy('name')->get(), 'locationOptions' => Location::orderBy('name')->get(), 'applications' => $user->role === 'creator' ? CampaignApplication::whereHas('creatorProfile', fn ($q) => $q->where('user_id', $user->id))->with('campaign')->latest('id')->get() : collect()])->title('Campaign opportunities');
    }
}
