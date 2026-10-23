<div class="container section discovery-page" x-data="{ filtersOpen: false }">
<span class="eyebrow">GOOD WORK STARTS WITH THE RIGHT PEOPLE</span>
<div class="section-heading">
<div>
<h1>Find your <span class="serif-word">creative people.</span>
</h1>
<p>Local voices. Original perspectives. Your next collaboration.</p>
</div>
<button class="button button-outline mobile-filter-toggle" @click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen">
<x-market-icon name="filter"/>Filters</button>
</div>
<x-workspace-errors/>
<div class="discovery-layout">
<aside class="filter-panel" :class="{ 'filters-open': filtersOpen }">
<div class="filter-heading">
<strong>Refine your search</strong>
<button wire:click="clearFilters" class="text-link">Reset</button>
</div>
<label>Search creators<input type="search" wire:model.live.debounce.300ms="search" placeholder="Name or username">
</label>
<label>Location<select wire:model.live="city">
<option value="">All locations</option>@foreach($locations as $location)<option value="{{ $location->slug }}">{{ $location->name }}</option>@endforeach</select>
</label>
<label>Category<select wire:model.live="category">
<option value="">All categories</option>@foreach($categories as $option)<option value="{{ $option->slug }}">{{ $option->name }}</option>@endforeach</select>
</label>
<label>Platform<select wire:model.live="platform">
<option value="">All platforms</option>
<option value="instagram">Instagram</option>
<option value="youtube">YouTube</option>
<option value="ugc">UGC</option>
<option value="other">Other</option>
</select>
</label>
<div class="form-grid">
<label>Min followers<input type="number" min="0" wire:model.live.debounce.400ms="minFollowers" placeholder="0">
</label>
<label>Max followers<input type="number" min="0" wire:model.live.debounce.400ms="maxFollowers" placeholder="Any">
</label>
</div>
<label>Budget up to (₹)<input type="number" min="0" wire:model.live.debounce.400ms="budget" placeholder="Any budget">
</label>
<label>Language<select wire:model.live="language">
<option value="">All languages</option>@foreach($languages as $option)<option value="{{ $option->slug }}">{{ $option->name }}</option>@endforeach</select>
</label>
<label class="checkbox-label">
<input type="checkbox" wire:model.live="available">Available for work</label>
<label class="checkbox-label">
<input type="checkbox" wire:model.live="verified">Verified profile</label>
<small>Follower metrics are self-reported unless marked verified.</small>
</aside>
<div>
<div class="results-toolbar">
<span>
<strong>{{ $creators->total() }}</strong> {{ Str::plural('creator',$creators->total()) }} to discover</span>
<label>
<span class="sr-only">Sort by</span>
<select wire:model.live="sort">
<option value="recent">Recently joined</option>
<option value="price-asc">Price: low to high</option>
<option value="price-desc">Price: high to low</option>
<option value="relevant">Relevant match · newest first</option>
</select>
</label>
</div>
<div wire:loading.delay class="loading-state" role="status">
<span class="sr-only">Finding your creative match…</span>
<div class="skeleton-grid" aria-hidden="true">
<div>
</div>
<div>
</div>
<div>
</div>
</div>
</div>
<div wire:loading.remove>
<div class="creator-grid discovery-results">@forelse($creators as $creator)<div wire:key="creator-{{ $creator->id }}">
<x-creator-card :creator="$creator"/>@if(auth()->user()?->role === 'brand')<button class="save-creator" wire:click="saveCreator({{ $creator->id }})">
<x-market-icon name="heart"/>Save creator</button>@endif</div>@empty<div class="empty-state">
<div class="empty-symbol">✳</div>
<span class="eyebrow">GOOD CONNECTIONS TAKE A LITTLE TIME</span>
<h2>No creators here just yet.</h2>
<p>Try another location or category, or be one of the first creative voices in our community.</p>
<div class="hero-actions">
<button wire:click="clearFilters" class="button button-outline">Clear filters</button>
<a class="button button-purple" href="{{ route('register',['role'=>'creator']) }}">Join as a creator ↗</a>
</div>
</div>@endforelse</div>{{ $creators->links() }}</div>
</div>
</div>
</div>
