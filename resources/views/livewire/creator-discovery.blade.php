<div class="container section discovery-page" x-data @if(request('filters') === 'open') x-init="$nextTick(() => $wire.openFilters().then(() => $refs.filters.showModal()))" @endif @filters-applied.window="$refs.filters.close()">
<span class="eyebrow">Discover your next creative partner</span>
<div class="section-heading"><div><h1>Find your creative partner.</h1><p>Compare original work, platforms, and services. Find the right fit for your project.</p></div></div>
<div class="discovery-platforms" aria-label="Filter by creator platform">
    @foreach(['' => 'All creators', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'ugc' => 'UGC creators'] as $value => $label)
        <button type="button" wire:click="$set('platform', '{{ $value }}')" @class(['is-active' => $platform === $value]) aria-pressed="{{ $platform === $value ? 'true' : 'false' }}">{{ $label }}</button>
    @endforeach
</div>
<div class="mobile-discovery-tools">
    <label><span class="sr-only">Search creators</span><input type="search" wire:model.live.debounce.500ms="search" placeholder="Name or username"></label>
    <button type="button" class="button button-outline" @click="$wire.openFilters().then(() => $refs.filters.showModal())" aria-haspopup="dialog"><x-market-icon name="filter" />Filters <span class="filter-count">{{ count(collect($activeFilters)->except('search')) }}</span></button>
</div>
<x-creator-filter-drawer :locations="$locations" :categories="$categories" :languages="$languages" :draft-filters="$draftFilters" />
<x-workspace-errors/>
<div class="discovery-layout">
<aside class="filter-panel desktop-filters" aria-label="Filter creators">
    <x-creator-filters :locations="$locations" :categories="$categories" :languages="$languages" :values="['city' => $city, 'category' => $category, 'platform' => $platform, 'language' => $language]" :advanced-active="$minFollowers !== '' || $maxFollowers !== '' || $language !== '' || $available || $verified" />
</aside>

<div class="discovery-main">
<div class="results-toolbar">
    <span class="results-count" role="status"><strong>{{ $creators->total() }} {{ Str::plural('creator', $creators->total()) }}</strong><span>{{ count($activeFilters) ? 'matching your search' : 'to discover' }}</span></span>
    <x-filter-select class="sort-select" model="sort" label="Sort by" :value="$sort" :options="['recommended' => 'Recommended', 'followers' => 'Most followers', 'recent' => 'Recently joined', 'price-asc' => 'Price: low to high', 'price-desc' => 'Price: high to low']" />
</div>
<x-active-creator-filters :filters="$activeFilters" />
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
<x-creator-card :creator="$creator"/>@if(in_array(auth()->user()?->role, ['brand', 'customer'], true))<button class="save-creator" wire:click="saveCreator({{ $creator->id }})">
<x-market-icon name="heart"/>Save creator</button>@endif</div>@empty
<div class="discovery-empty">
    <span class="discovery-empty-icon"><x-market-icon name="search" /></span>
    <h2>{{ count($activeFilters) ? 'No matches for this search.' : 'No creators here just yet.' }}</h2>
    <p>{{ count($activeFilters) ? 'Try a different category, a nearby city, or a wider budget. Your next creative connection could be one filter away.' : 'Our local creator community is taking shape. Check back soon, or join and put your work in front of brands.' }}</p>
    <div class="discovery-empty-actions">
        @if(count($activeFilters))<x-colliva-button wire:click="clearFilters">Clear filters <x-market-icon name="arrow-right" /></x-colliva-button>@endif
        <x-colliva-button :href="route('for-creators')" variant="outline">Join as Creator</x-colliva-button>
    </div>
</div>
@if(count($activeFilters))
<div class="discovery-suggestions"><span>Explore another category</span><div>@foreach($categories->where('slug', '!=', $category)->take(4) as $suggestion)<a href="{{ route('creators', ['category' => $suggestion->slug]) }}">{{ $suggestion->name }}<x-market-icon name="arrow-up-right" /></a>@endforeach</div></div>
@endif
@endforelse</div>{{ $creators->links() }}</div>
</div>
</div>
</div>
