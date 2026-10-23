<div class="container section workspace-page">
<span class="eyebrow">A SPACE FOR YOUR NEXT BIG IDEA</span>
<div class="section-heading">
<div>
<h1>Your <span class="serif-word">brand workspace.</span>
</h1>
<p>Tell your story. Find your people. Make something good.</p>
</div>
<a class="button button-purple" href="{{ route('campaigns') }}">Create a campaign ↗</a>
</div>
<x-workspace-errors/>
<section class="workspace-card">
<h2>A little about your business.</h2>
<form wire:submit="save">
<div class="form-grid">
<label>Business name<input wire:model="companyName" required autocomplete="organization" maxlength="150">
</label>
<label>Business website (optional)<input type="url" wire:model="website" placeholder="https://">
</label>
</div>
<label>What’s your story?<textarea wire:model="description" required maxlength="3000">
</textarea>
</label>
<label>City<select wire:model="city" required>
<option value="">Choose your city</option>@foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select>
</label>
<button class="button button-purple" wire:loading.attr="disabled">Save business profile</button>
</form>
</section>
<section class="workspace-card">
<h2>Your creative shortlist.</h2>@forelse($savedCreators as $creator)<div class="item-row" wire:key="saved-{{ $creator->id }}">
<div>
<a class="text-link" href="{{ route('creator.show',$creator->slug) }}">{{ $creator->display_name }} ↗</a>
<small>{{ $creator->location->name }} · {{ $creator->category->name }}</small>
</div>
<button class="danger" wire:click="removeSaved({{ $creator->id }})">Remove</button>
</div>@empty<p>Your shortlist is a blank canvas. Save creators while exploring to find them here.</p>
<a class="text-link" style="margin-top:20px" href="{{ route('creators') }}">Discover creators ↗</a>@endforelse</section>
</div>
