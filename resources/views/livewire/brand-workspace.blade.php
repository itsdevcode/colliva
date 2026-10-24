<div class="container section workspace-page">
<span class="eyebrow">A SPACE FOR YOUR NEXT BIG IDEA</span>
<div class="section-heading">
<div>
<h1>Your <span class="serif-word">brand workspace.</span>
</h1>
<p>Tell your story. Find your people. Make something good.</p>
</div>
<a class="button button-purple" href="{{ route('campaigns') }}">Create a campaign <x-market-icon name="arrow-up-right" /></a>
</div>
<a class="text-link" href="{{ route('brand.dashboard') }}">Go to your dashboard <x-market-icon name="arrow-right" /></a><x-workspace-errors/>
<section class="workspace-card">
<h2>A little about your business.</h2>
<form wire:submit="save">
<div class="form-grid">
<label>Business name<input @error('companyName') aria-invalid="true" aria-describedby="error-companyName" @enderror wire:model="companyName" required autocomplete="organization" maxlength="150">
<x-field-error name="companyName" /></label>
<label>Business website (optional)<input @error('website') aria-invalid="true" aria-describedby="error-website" @enderror type="url" wire:model="website" placeholder="https://">
<x-field-error name="website" /></label>
</div>
<label>What’s your story?<textarea @error('description') aria-invalid="true" aria-describedby="error-description" @enderror wire:model="description" required maxlength="3000">
</textarea>
<x-field-error name="description" /></label>
<label>City<select @error('city') aria-invalid="true" aria-describedby="error-city" @enderror wire:model="city" required>
<option value="">Choose your city</option>@foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select>
<x-field-error name="city" /></label>
<button class="button button-purple" wire:loading.attr="disabled">Save business profile</button>
</form>
</section>
<section class="workspace-card">
<h2 id="saved-creators">Your creative shortlist.</h2>@forelse($savedCreators as $creator)<div class="item-row" wire:key="saved-{{ $creator->id }}">
<div>
<a class="text-link" href="{{ route('creator.show',$creator->slug) }}">{{ $creator->display_name }} <x-market-icon name="arrow-up-right" /></a>
<small>{{ $creator->location->name }} · {{ $creator->category->name }}</small>
</div>
<button class="danger" wire:click="removeSaved({{ $creator->id }})">Remove</button>
</div>@empty<x-colliva-empty title="Your shortlist starts here." description="Save creators as you explore. Their profiles will be ready whenever your next idea arrives." icon="heart"><x-colliva-button :href="route('creators')" variant="outline">Discover Creators <x-market-icon name="arrow-right" /></x-colliva-button></x-colliva-empty>@endforelse</section>
</div>
