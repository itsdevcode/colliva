@props(['locations', 'categories', 'languages', 'values' => [], 'advancedActive' => false])
<div class="filter-heading"><strong><x-market-icon name="filter" />Filters</strong><button type="button" wire:click="clearFilters" class="text-link">Reset</button></div>
<label>Search creators<input type="search" wire:model.live.debounce.300ms="search" placeholder="Name or username"></label>
<x-filter-select model="city" label="Location" placeholder="All locations" :value="$values['city'] ?? ''" :options="$locations->pluck('name', 'slug')->all()" />
<x-filter-select model="category" label="Category" placeholder="All categories" :value="$values['category'] ?? ''" :options="$categories->pluck('name', 'slug')->all()" />
<x-filter-select model="platform" label="Platform" placeholder="All platforms" :value="$values['platform'] ?? ''" :options="['instagram' => 'Instagram', 'youtube' => 'YouTube', 'ugc' => 'UGC', 'other' => 'Other']" />
<label>Maximum budget (₹)<input type="number" min="0" wire:model.live.debounce.400ms="budget" placeholder="Any budget"></label>
<details class="advanced-filters" @if($advancedActive) open @endif wire:ignore.self>
    <summary>Audience &amp; availability <span aria-hidden="true">+</span></summary>
    <div class="advanced-filter-fields">
        <div class="form-grid"><label>Min followers<input type="number" min="0" wire:model.live.debounce.400ms="minFollowers" placeholder="0"></label><label>Max followers<input type="number" min="0" wire:model.live.debounce.400ms="maxFollowers" placeholder="Any"></label></div>
        <x-filter-select model="language" label="Language" placeholder="All languages" :value="$values['language'] ?? ''" :options="$languages->pluck('name', 'slug')->all()" />
        <label class="checkbox-label"><input type="checkbox" wire:model.live="available">Available for work</label>
        <label class="checkbox-label"><input type="checkbox" wire:model.live="verified">Verified profile</label>
    </div>
</details>
<small class="filter-footnote"><x-market-icon name="shield" />Follower metrics are self-reported unless marked verified.</small>
