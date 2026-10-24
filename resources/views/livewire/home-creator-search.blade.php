<div class="creator-search-shell" x-data @filters-applied.window="$refs.filters.close()">
    <div class="creator-search-toolbar">
        <form wire:submit="searchCreators" class="creator-search-input" role="search" aria-label="Find creators">
            <x-market-icon name="search" />
            <label class="sr-only" for="home-creator-search">Search creators by name or style</label>
            <input id="home-creator-search" type="search" wire:model.live.debounce.500ms="search" placeholder="Name or style" enterkeyhint="search" autocomplete="off">
            <button type="submit" class="icon-button" aria-label="Search creators" wire:loading.attr="disabled"><x-market-icon name="arrow-right" /></button>
        </form>
        <div class="creator-search-city"><x-filter-select model="city" label="City" placeholder="Any city" :value="$city" :options="$locations->pluck('name', 'slug')->all()" icon="pin" /></div>
        <button type="button" class="creator-search-more" @click="$wire.openFilters().then(() => $refs.filters.showModal())" aria-label="All filters{{ count(collect($activeFilters)->except('search')) ? ', '.count(collect($activeFilters)->except('search')).' active' : '' }}" aria-haspopup="dialog">
            <x-market-icon name="filter" /><span class="creator-search-filter-label">All Filters</span>@if(count(collect($activeFilters)->except('search')))<span class="filter-count">{{ count(collect($activeFilters)->except('search')) }}</span>@endif
        </button>
    </div>
    <div class="creator-search-presets" role="group" aria-label="Quick filters">
        @foreach([
            ['fashion', 'Fashion', $category === 'fashion'],
            ['beauty', 'Beauty', $category === 'beauty'],
            ['ugc', 'UGC', $platform === 'ugc'],
            ['budget', 'Under ₹5,000', $budget === '5000'],
            ['available', 'Available', $available],
        ] as [$preset, $label, $selected])
            @if(! in_array($preset, ['fashion', 'beauty'], true) || $categories->contains('slug', $preset))
                <button type="button" wire:click="toggleQuickFilter('{{ $preset }}')" @class(['creator-search-preset', 'is-selected' => $selected]) aria-pressed="{{ $selected ? 'true' : 'false' }}">@if($selected)<x-market-icon name="check" />@endif{{ $label }}</button>
            @endif
        @endforeach
    </div>
    <x-active-creator-filters :filters="$activeFilters" />
    <div class="creator-search-results">
        <span role="status" aria-live="polite"><span wire:loading.remove>{{ number_format($resultCount) }} {{ Str::plural('creator', $resultCount) }} found</span><span wire:loading.delay>Updating…</span></span>
        <a class="text-link" href="{{ $this->discoveryUrl() }}">View results <x-market-icon name="arrow-right" /></a>
    </div>
    <x-creator-filter-drawer :locations="$locations" :categories="$categories" :languages="$languages" :draft-filters="$draftFilters" />
</div>
