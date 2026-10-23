@props(['name', 'label', 'placeholder', 'options'])
<div class="discovery-picker" x-data="{ value: '', labels: @js($options->pluck('name', 'slug')->prepend($placeholder, '')->all()) }">
    <input type="hidden" name="{{ $name }}" :value="value">
    <flux:dropdown position="bottom" align="start">
        <button type="button" class="discovery-picker-trigger">
            <span class="picker-copy"><span class="field-caption">{{ $label }}</span><span class="picker-value" x-text="labels[value]">{{ $placeholder }}</span></span>
            <svg class="picker-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m5 8 5 5 5-5" /></svg>
        </button>
        <flux:menu class="discovery-picker-menu">
            <flux:menu.radio.group x-model="value">
                <flux:menu.radio value="">{{ $placeholder }}</flux:menu.radio>
                @foreach($options as $option)<flux:menu.radio value="{{ $option->slug }}">{{ $option->name }}</flux:menu.radio>@endforeach
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
</div>
