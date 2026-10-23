@props(['model', 'label', 'options', 'value' => '', 'placeholder' => null])
@php($selectedLabel = $options[$value] ?? $placeholder ?? 'Choose an option')
<div {{ $attributes->class('filter-select') }}>
    <span class="filter-select-label">{{ $label }}</span>
    <flux:dropdown position="bottom" align="start">
        <button type="button" class="filter-select-trigger" aria-label="{{ $label }}: {{ $selectedLabel }}">
            <span>{{ $selectedLabel }}</span>
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m5 8 5 5 5-5" /></svg>
        </button>
        <flux:menu class="filter-select-menu">
            <flux:menu.radio.group wire:model.live="{{ $model }}">
                @if($placeholder !== null)<flux:menu.radio value="">{{ $placeholder }}</flux:menu.radio>@endif
                @foreach($options as $optionValue => $optionLabel)<flux:menu.radio value="{{ $optionValue }}">{{ $optionLabel }}</flux:menu.radio>@endforeach
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
</div>
