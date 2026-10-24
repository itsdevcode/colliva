<div class="container section workspace-page">
<span class="eyebrow">BRING GOOD IDEAS TO LIFE</span>
<div class="section-heading">
<div>
<h1>Made for <span class="serif-word">collaboration.</span>
</h1>
<p>{{ auth()->user()->role === 'brand' ? 'Publish a brief. Meet your next creative partner.' : 'Find opportunities that fit your creative lane.' }}</p>
</div>
</div>
<x-workspace-errors/>
@if(auth()->user()->role === 'brand')<section class="workspace-card">
@if(!$formOpen)<button class="text-link" wire:click="newCampaign" aria-expanded="{{ $formOpen ? 'true' : 'false' }}">
<x-market-icon name="briefcase"/>Create a new campaign +</button>@endif
@if($formOpen)
<form wire:submit="{{ $campaignStep < 3 ? 'nextCampaignStep' : 'createCampaign(true)' }}" style="margin-top:25px">
<h2>{{ $editingCampaignId ? 'Edit your campaign draft.' : 'Start a new campaign.' }}</h2>
<div class="form-progress" role="status"><span>Step {{ $campaignStep }} of 3 · {{ ['Your idea', 'The creative brief', 'Timing & review'][$campaignStep - 1] ?? 'Your idea' }}</span><progress max="3" value="{{ $campaignStep }}">{{ $campaignStep }} of 3</progress></div>
@if($campaignStep === 1)
<label>Campaign title<input @error('title') aria-invalid="true" aria-describedby="error-title" @enderror wire:model="title" required maxlength="150">
<x-field-error name="title" /></label>
<label>The idea<textarea @error('description') aria-invalid="true" aria-describedby="error-description" @enderror wire:model="description" required>
</textarea>
<x-field-error name="description" /></label>
<div class="form-grid">
<label>Category<select @error('category') aria-invalid="true" aria-describedby="error-category" @enderror wire:model="category" required>
<option value="">Choose a category</option>@foreach($categories as $option)<option value="{{ $option->id }}">{{ $option->name }}</option>@endforeach</select>
<x-field-error name="category" /></label>
<label>Total budget (₹)<input inputmode="decimal" @error('budget') aria-invalid="true" aria-describedby="error-budget" @enderror type="number" min="1" step=".01" wire:model="budget" required>
<x-field-error name="budget" /></label>
</div>
@elseif($campaignStep === 2)
<fieldset>
<legend>Target cities</legend>
<div class="checkbox-group">@foreach($locationOptions as $location)<label class="checkbox-label">
<input type="checkbox" wire:model="locations" value="{{ $location->id }}">{{ $location->name }}</label>@endforeach</div>
</fieldset>
<fieldset>
<legend>Required platforms</legend>
<div class="checkbox-group">@foreach(['instagram','youtube','ugc','other'] as $option)<label class="checkbox-label">
<input type="checkbox" wire:model="platforms" value="{{ $option }}">{{ ucfirst($option) }}</label>@endforeach</div>
</fieldset>
<label>Deliverables<textarea @error('deliverables') aria-invalid="true" aria-describedby="error-deliverables" @enderror wire:model="deliverables" required>
</textarea>
<x-field-error name="deliverables" /></label>
<label>Creator requirements<textarea @error('requirements') aria-invalid="true" aria-describedby="error-requirements" @enderror wire:model="requirements" required>
</textarea>
<x-field-error name="requirements" /></label>
@elseif($campaignStep === 3)
<div class="form-grid">
<label>Apply by<input @error('applicationDeadline') aria-invalid="true" aria-describedby="error-applicationDeadline" @enderror type="date" wire:model="applicationDeadline" required>
<x-field-error name="applicationDeadline" /></label>
<label>Deliver content by<input @error('deliveryDeadline') aria-invalid="true" aria-describedby="error-deliveryDeadline" @enderror type="date" wire:model="deliveryDeadline" required>
<x-field-error name="deliveryDeadline" /></label>
</div>
<div class="campaign-review"><h3>{{ $title }}</h3><p>{{ $description }}</p><p>{{ $deliverables }}</p><span>Budget ₹{{ number_format((float) $budget, 2) }} · {{ count($locations) }} locations · {{ implode(', ', $platforms) }}</span><p class="form-help">Your campaign remains private until approved by an administrator.</p></div>
@endif
<div class="campaign-actions">
@if($campaignStep > 1)<button type="button" class="button button-outline" wire:click="$set('campaignStep', {{ max(1, $campaignStep - 1) }})">Back</button>@endif
@if($campaignStep < 3)<button type="submit" class="button button-purple" wire:loading.attr="disabled">Continue <x-market-icon name="arrow-right" /></button>@else
<button class="button button-purple" wire:loading.attr="disabled">Submit for moderation <x-market-icon name="arrow-up-right" /></button>
<button type="button" class="button button-outline" wire:click="createCampaign(false)" wire:loading.attr="disabled">{{ $editingCampaignId ? 'Save changes' : 'Save draft' }}</button>
@endif
<button type="button" class="text-link" wire:click="cancelCampaignEdit" wire:confirm="Discard the changes in this form? Saved campaign drafts will be kept.">Cancel</button>
</div>
<p class="form-help">Complete your business profile first. Campaigns appear to creators after admin approval.</p>
</form>
@endif
</section>@endif
@forelse($campaigns as $campaign)<article class="workspace-card" wire:key="campaign-{{ $campaign->id }}">
<span class="status-badge">{{ $campaign->status }}</span>
<h2 style="margin-top:15px">{{ $campaign->title }}</h2>
<span class="eyebrow">{{ $campaign->brandProfile->company_name }} · {{ $campaign->category->name }}</span>
<p>{{ $campaign->description }}</p>
<div class="campaign-meta">
<span>Budget ₹{{ number_format($campaign->budget_minor/100,2) }}</span>
<span>Apply by {{ $campaign->application_deadline->format('d M Y') }}</span>
<span>Deliver by {{ $campaign->delivery_deadline->format('d M Y') }}</span>
<span>{{ $campaign->locations->pluck('name')->join(', ') }}</span>
<span>{{ $campaign->platforms->pluck('platform')->join(', ') }}</span>
</div>
<h3>Deliverables</h3>
<p>{{ $campaign->deliverables }}</p>
<h3 style="margin-top:20px">Requirements</h3>
<p>{{ $campaign->requirements }}</p>
@if(auth()->user()->role === 'brand')<div class="campaign-actions">@if($campaign->status === 'draft')<button class="button button-outline" wire:click="editCampaign({{ $campaign->id }})">Edit draft</button><button class="button button-purple" wire:click="changeStatus({{ $campaign->id }},'pending')">Submit for moderation</button>@endif @if($campaign->status === 'published')<button class="button button-outline" wire:click="changeStatus({{ $campaign->id }},'closed')">Close applications</button>@endif @if($campaign->status === 'closed')<button class="button button-outline" wire:click="changeStatus({{ $campaign->id }},'completed')">Mark completed</button>@endif @if(in_array($campaign->status,['draft','published','closed']))<button class="button button-outline" wire:click="changeStatus({{ $campaign->id }},'cancelled')">Cancel campaign</button>@endif</div>
<h3 style="margin-top:30px">Creator applications</h3>@forelse($campaign->applications as $application)<div class="collaboration-detail" wire:key="application-{{ $application->id }}">
<strong>{{ $application->creatorProfile->display_name }}</strong>
<span class="status-badge">{{ $application->status }}</span>
<p>{{ $application->proposal }}</p>
<p>Quote: ₹{{ number_format($application->quote_minor/100,2) }}</p>@if(in_array($application->status,['submitted','shortlisted']) && $campaign->status === 'published')<div class="campaign-actions">
<button class="button button-purple" wire:click="review({{ $application->id }},'accepted')">Accept proposal</button>
<button class="button button-outline" wire:click="review({{ $application->id }},'shortlisted')">Shortlist</button>
<button class="text-link" wire:click="review({{ $application->id }},'rejected')">Reject</button>
</div>@endif</div>@empty<p>No applications yet. The right connection is worth the wait.</p>@endforelse
@else @can('apply',$campaign)<form wire:submit="apply({{ $campaign->id }})" class="offer-form">
<label>Your proposal<textarea wire:model="proposals.{{ $campaign->id }}" required minlength="20" maxlength="3000" placeholder="Tell the brand how you would bring this idea to life.">
</textarea>
</label>
<label>Your quote (₹)<input inputmode="decimal" type="number" min="1" step=".01" wire:model="quotes.{{ $campaign->id }}" required>
</label>
<button class="button button-purple" wire:loading.attr="disabled">Send application <x-market-icon name="arrow-up-right" /></button>
</form>@else<p style="margin-top:20px">This opportunity requires an approved, available profile matching the campaign’s location, category and platforms.</p>@endcan @endif</article>@empty<div class="empty-state">
<div class="empty-symbol">✳</div>
<h2>Your next opportunity is taking shape.</h2>
<p>{{ auth()->user()->role === 'brand' ? 'Start with a campaign brief above. Give creators a clear idea of what you want to make together.' : 'Published campaigns will appear here. Keep your profile ready for the right one.' }}</p>
</div>@endforelse
{{ $campaigns->links() }}
@if(auth()->user()->role === 'creator')<section class="workspace-card" style="margin-top:30px">
<h2>Your applications.</h2>@forelse($applications as $application)<div class="item-row" wire:key="own-application-{{ $application->id }}">
<div>
<strong>{{ $application->campaign->title }}</strong>
<small>{{ $application->status }} · ₹{{ number_format($application->quote_minor/100,2) }}</small>
</div>@if(in_array($application->status,['submitted','shortlisted']))<button class="text-link" wire:click="withdraw({{ $application->id }})">Withdraw</button>@endif</div>@empty<p>No applications yet. Your next creative chapter is out there.</p>@endforelse</section>@endif<div wire:loading.delay class="form-help" role="status">Updating your workspace…</div>
</div>
