<x-layouts::marketplace :title="$article->title" :description="$article->summary">
<div class="container section support-form-page">
<nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('support') }}">Support Center</a><span>/</span><a href="{{ route('support', ['category' => $article->category->slug]) }}">{{ $article->category->name }}</a><span>/</span><span>Guide</span></nav>
<article class="support-guide"><span class="eyebrow">{{ $article->category->name }}</span><h1>{{ $article->title }}</h1><p class="support-guide-summary">{{ $article->summary }}</p><p class="support-form-note">Updated {{ $article->updated_at->format('j F Y') }}</p><div class="support-guide-content">{{ $article->content }}</div></article>
@if($related->isNotEmpty())<section class="support-related"><h2>Keep exploring.</h2>@foreach($related as $item)<a class="support-related-link" href="{{ route('support.article', $item->slug) }}">{{ $item->title }}<x-market-icon name="arrow-right" /></a>@endforeach</section>@endif
<section class="support-contact-banner"><div><h2>Still need a hand?</h2><p>Send us the details and we’ll help you find a way forward.</p></div><x-colliva-button :href="route('support.contact')">Contact support</x-colliva-button></section>
</div>
</x-layouts::marketplace>
