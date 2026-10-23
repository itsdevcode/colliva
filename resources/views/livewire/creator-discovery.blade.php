<div class="container section discovery-page" x-data>
<span class="eyebrow">Discover your next creative partner</span>
<div class="section-heading">
    <div><h1>Find creators for your next campaign.</h1><p>Compare original work, platforms, and services. Find the right fit for your brand.</p></div>
</div>
<div class="discovery-platforms" aria-label="Filter by creator platform">
    @foreach(['' => 'All creators', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'ugc' => 'UGC creators'] as $value => $label)
        <button type="button" wire:click="$set('platform', '{{ $value }}')" @class(['is-active' => $platform === $value]) aria-pressed="{{ $platform === $value ? 'true' : 'false' }}">{{ $label }}</button>
    @endforeach
</div>
<div class="mobile-discovery-tools">
    <label><span class="sr-only">Search creators</span><input type="search" wire:model.live.debounce.300ms="search" placeholder="Name or username"></label>
    <button type="button" class="button button-outline" @click="$refs.filters.showModal()" aria-haspopup="dialog"><x-market-icon name="filter" />Filters</button>
</div>
<x-colliva-drawer name="filters" title="Filter creators" wire:ignore.self>
    <div class="filter-panel mobile-filter-panel"><x-creator-filters :locations="$locations" :categories="$categories" :languages="$languages" :advanced-active="$minFollowers !== '' || $maxFollowers !== '' || $language !== '' || $available || $verified" /></div>
    <button type="button" class="button button-purple drawer-done" @click="$refs.filters.close()">Show {{ $creators->total() }} {{ Str::plural('creator', $creators->total()) }}</button>
</x-colliva-drawer>
<x-workspace-errors/>
<div class="discovery-layout">
<aside class="filter-panel desktop-filters" aria-label="Filter creators">
    <x-creator-filters :locations="$locations" :categories="$categories" :languages="$languages" :advanced-active="$minFollowers !== '' || $maxFollowers !== '' || $language !== '' || $available || $verified" />
</aside>

<div>
<div class="results-toolbar" aria-live="polite" aria-atomic="true">
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
<x-market-icon name="heart"/>Save creator</button>@endif</div>@empty
<x-colliva-empty title="No creators here just yet." description="Try a broader search or clear your filters. If you create original content, this could be your place to start.">
    <x-colliva-button wire:click="clearFilters" variant="outline">Clear filters</x-colliva-button>
    <x-colliva-button :href="route('register', ['role' => 'creator'])">Join as Creator</x-colliva-button>
</x-colliva-empty>
@endforelse</div>{{ $creators->links() }}</div>
</div>
</div>
</div>
