<div class="creator-search-shell" x-data @filters-applied.window="$refs.filters.close()">
    <form wire:submit="searchCreators" class="creator-search-form" role="search" aria-label="Find creators">
        <div class="creator-search-query">
            <x-market-icon name="search" />
            <label class="sr-only" for="home-creator-search">Search creators or niches</label>
            <input id="home-creator-search" type="search" wire:model="search" placeholder="Search creators or niches" enterkeyhint="search" autocomplete="off" maxlength="100">
        </div>
        <div class="creator-search-city"><x-filter-select model="city" label="City" placeholder="Any city" :value="$city" :options="$locations->pluck('name', 'slug')->all()" icon="pin" /></div>
        <button type="submit" class="creator-search-submit" aria-label="Search creators" wire:loading.attr="disabled" wire:target="searchCreators">
            <span class="creator-search-submit-label">Search</span>
            <x-market-icon name="arrow-right" />
        </button>
        <div class="creator-search-presets" role="group" aria-label="Quick filters">
            @foreach([
                ['fashion', 'Fashion', $category === 'fashion'],
                ['beauty', 'Beauty', $category === 'beauty'],
                ['ugc', 'UGC', $platform === 'ugc'],
                ['budget', 'Under ₹5,000', $budget === '5000'],
                ['available', 'Available', $available],
            ] as [$preset, $label, $selected])
                @if(! in_array($preset, ['fashion', 'beauty'], true) || $categories->contains('slug', $preset))
                    <button type="button" wire:key="home-preset-{{ $preset }}" wire:click="toggleQuickFilter('{{ $preset }}')" @class(['creator-search-preset', 'is-selected' => $selected]) aria-pressed="{{ $selected ? 'true' : 'false' }}">{{ $label }}</button>
                @endif
            @endforeach
        </div>
        <button type="button" class="creator-search-more" @click="$wire.openFilters().then(() => $refs.filters.showModal())" aria-label="All filters{{ count(collect($activeFilters)->except('search', 'city')) ? ', '.count(collect($activeFilters)->except('search', 'city')).' active' : '' }}" aria-haspopup="dialog">
            <x-market-icon name="filter" /><span>All filters</span>@if(count(collect($activeFilters)->except('search', 'city')))<span class="creator-search-active-dot" aria-hidden="true"></span>@endif
        </button>
    </form>
    <span class="sr-only" role="status" wire:loading.delay>Updating search preferences…</span>
    <x-creator-filter-drawer :locations="$locations" :categories="$categories" :languages="$languages" :draft-filters="$draftFilters" />
</div>
