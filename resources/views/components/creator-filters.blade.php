@props(['locations', 'categories', 'languages'])
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
