@props(['creator'])
<article class="creator-card">
<a class="creator-visual" href="{{ route('creator.show',$creator->slug) }}">@if($creator->portfolioItems->firstWhere('image_path','!=',null))<img src="{{ Storage::disk(config('filesystems.media_disk'))->url($creator->portfolioItems->firstWhere('image_path','!=',null)->image_path) }}" alt="Portfolio by {{ $creator->display_name }}" loading="lazy" decoding="async" width="600" height="750">@else<div class="portfolio-placeholder">
<x-market-icon name="play"/>
<span>Explore their work</span>
</div>@endif<span class="card-category">{{ $creator->category->name }}</span>@if($creator->available)<span class="availability-indicator" title="Available for work"><span class="sr-only">Available for work</span>
</span>@endif</a>
<div class="creator-details">
<div class="creator-identity">@if($creator->photo_path)<img src="{{ Storage::disk(config('filesystems.media_disk'))->url($creator->photo_path) }}" alt="" loading="lazy" width="40" height="40">@endif<div>
<a href="{{ route('creator.show',$creator->slug) }}">{{ $creator->display_name }} @if($creator->verified_at)<span class="sr-only">Verified profile</span><x-market-icon name="shield"/>@endif</a>
<span>
<x-market-icon name="pin"/>{{ $creator->location->name }}</span>
</div>
</div>
<p class="creator-username">{{ '@'.$creator->slug }}</p><p class="creator-bio">{{ Str::limit($creator->bio, 120) }}</p>
<div class="creator-platforms" aria-label="Creator platforms">@foreach($creator->socialAccounts as $account)<span><x-market-icon :name="$account->platform === 'instagram' ? 'camera' : ($account->platform === 'youtube' ? 'play' : 'globe')" />{{ ucfirst($account->platform) }}@if($account->followers !== null) · {{ number_format($account->followers) }} followers @endif</span>@endforeach</div>
<div class="creator-card-bottom">
<span>@if($creator->service_packages_min_price_minor !== null)From <strong>₹{{ number_format($creator->service_packages_min_price_minor/100) }}</strong>@else Enquire for pricing @endif
</span>
<a href="{{ route('creator.show',$creator->slug) }}" aria-label="View {{ $creator->display_name }}">
View profile <x-market-icon name="arrow-up-right"/>
</a>
</div>
</div>
</article>
