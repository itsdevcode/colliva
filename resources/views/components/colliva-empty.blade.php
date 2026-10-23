@props(['title', 'description', 'icon' => 'camera'])
<div {{ $attributes->class('empty-state') }}>
    <span class="empty-icon"><x-market-icon :name="$icon" /></span>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
    <div class="hero-actions">{{ $slot }}</div>
</div>
