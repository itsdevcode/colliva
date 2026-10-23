@props(['eyebrow' => null, 'title', 'description' => null])
<div {{ $attributes->class('section-heading') }}>
    <div>
        @if($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
        <h2>{{ $title }}</h2>
        @if($description)<p>{{ $description }}</p>@endif
    </div>
    {{ $slot }}
</div>
