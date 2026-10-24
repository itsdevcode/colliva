@props(['locations', 'categories', 'languages', 'draftFilters' => []])
<x-colliva-drawer name="filters" title="Filter creators" wire:ignore.self class="creator-filter-drawer">
    <form wire:submit="applyFilters" class="mobile-filter-form">
        <p class="quiet">Choose what matters. Your results update when you apply.</p>
        <x-workspace-errors />
        @foreach(['city' => ['City', $locations], 'category' => ['Category', $categories], 'language' => ['Language', $languages]] as $property => [$label, $options])
            <x-filter-select :model="'draftFilters.'.$property" :label="$label" :placeholder="'Any '.strtolower($label)" :value="$draftFilters[$property] ?? ''" :options="$options->pluck('name', 'slug')->all()" :live="false" />
        @endforeach
        <x-filter-select model="draftFilters.platform" label="Platform" placeholder="All platforms" :value="$draftFilters['platform'] ?? ''" :options="['instagram' => 'Instagram', 'youtube' => 'YouTube', 'ugc' => 'UGC', 'other' => 'Other']" :live="false" />
        <label>Maximum budget (₹)<input type="number" inputmode="numeric" min="0" max="100000000" wire:model="draftFilters.budget" placeholder="Any budget"></label>
        <details class="advanced-filters" @if(($draftFilters['minFollowers'] ?? '') !== '' || ($draftFilters['maxFollowers'] ?? '') !== '' || ($draftFilters['available'] ?? false) || ($draftFilters['verified'] ?? false)) open @endif>
            <summary>Audience &amp; availability <span aria-hidden="true">+</span></summary>
            <div class="advanced-filter-fields">
                <label>Minimum followers<input type="number" inputmode="numeric" min="0" wire:model="draftFilters.minFollowers" placeholder="No minimum"></label>
                <label>Maximum followers<input type="number" inputmode="numeric" min="0" wire:model="draftFilters.maxFollowers" placeholder="No maximum"></label>
                <label class="checkbox-label"><input type="checkbox" wire:model="draftFilters.available">Available for work</label>
                <label class="checkbox-label"><input type="checkbox" wire:model="draftFilters.verified">Verified profiles</label>
            </div>
        </details>
        <div class="drawer-actions"><button type="button" class="button button-outline" wire:click="clearDraftFilters" wire:loading.attr="disabled">Clear all</button><button type="submit" class="button button-purple" wire:loading.attr="disabled">Apply filters <x-market-icon name="check" /></button></div>
    </form>
</x-colliva-drawer>
