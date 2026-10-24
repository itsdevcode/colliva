@props(['filters'])
@if(count($filters))
    <div class="active-filter-row" aria-label="Active filters">
        @foreach($filters as $property => $label)
            <button type="button" class="active-filter-chip" @if(in_array($property, ['available', 'verified'], true)) wire:click="$set('{{ $property }}', false)" @else wire:click="$set('{{ $property }}', '')" @endif aria-label="Remove {{ $label }}">{{ $label }}<span aria-hidden="true">×</span></button>
        @endforeach
        <button type="button" class="clear-all-filters" wire:click="clearFilters">Clear all</button>
    </div>
@endif
