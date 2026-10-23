@props(['item'])
<article {{ $attributes->class('portfolio-tile') }}>
    @if($item->image_path)
        <img src="{{ Storage::disk(config('filesystems.media_disk'))->url($item->image_path) }}" alt="{{ $item->title }}" loading="lazy" width="720" height="900">
    @endif
    <h3>{{ $item->title }}</h3>
    @if($item->description)<p>{{ $item->description }}</p>@endif
    @if($item->video_url)<a class="text-link" href="{{ $item->video_url }}" target="_blank" rel="noopener noreferrer">Watch original work <x-market-icon name="arrow-up-right" /></a>@endif
</article>
