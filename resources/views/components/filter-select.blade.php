@props(['model', 'label', 'options', 'value' => '', 'placeholder' => null, 'live' => true, 'icon' => null])
@php($selectedLabel = $options[$value] ?? $placeholder ?? 'Choose an option')
<div {{ $attributes->class('filter-select') }} x-data="{ labels: @js($options), placeholder: @js($placeholder ?? 'Choose an option') }">
    <span class="filter-select-label">{{ $label }}</span>
    <flux:dropdown position="bottom" align="start">
        <button type="button" class="filter-select-trigger" aria-label="{{ $label }}: {{ $selectedLabel }}" :aria-label="@js($label.': ') + (labels[$wire.$get(@js($model))] ?? placeholder)">
            @if($icon)<x-market-icon :name="$icon" />@endif
            <span x-text="labels[$wire.$get(@js($model))] ?? placeholder">{{ $selectedLabel }}</span>
            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m5 8 5 5 5-5" /></svg>
        </button>
        <flux:menu class="filter-select-menu">
            @if($live)
                <flux:menu.radio.group wire:model.live="{{ $model }}">
                    @if($placeholder !== null)<flux:menu.radio value="">{{ $placeholder }}</flux:menu.radio>@endif
                    @foreach($options as $optionValue => $optionLabel)<flux:menu.radio value="{{ $optionValue }}">{{ $optionLabel }}</flux:menu.radio>@endforeach
                </flux:menu.radio.group>
            @else
                <flux:menu.radio.group wire:model="{{ $model }}">
                    @if($placeholder !== null)<flux:menu.radio value="">{{ $placeholder }}</flux:menu.radio>@endif
                    @foreach($options as $optionValue => $optionLabel)<flux:menu.radio value="{{ $optionValue }}">{{ $optionLabel }}</flux:menu.radio>@endforeach
                </flux:menu.radio.group>
            @endif
        </flux:menu>
    </flux:dropdown>
</div>
