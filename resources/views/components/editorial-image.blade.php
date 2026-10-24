@props(['name', 'alt' => '', 'priority' => false, 'sizes' => '(min-width: 900px) 50vw, 100vw'])
@php($portrait = in_array($name, ['fashion-creators.webp', 'food-creators.webp'], true))
<img src="{{ asset('images/'.str_replace('.webp', '-800.webp', $name)) }}"
     srcset="{{ asset('images/'.str_replace('.webp', '-400.webp', $name)) }} 400w, {{ asset('images/'.str_replace('.webp', '-800.webp', $name)) }} 800w, {{ asset('images/'.$name) }} {{ $portrait ? 1122 : 1536 }}w"
     sizes="{{ $sizes }}" alt="{{ $alt }}" width="{{ $portrait ? 1122 : 1536 }}" height="{{ $portrait ? 1402 : 1024 }}"
     @if($priority) fetchpriority="high" @else loading="lazy" @endif decoding="async" {{ $attributes }}>
