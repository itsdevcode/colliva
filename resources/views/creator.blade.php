<x-layouts::marketplace :title="$creator->display_name.' — '.$creator->category->name" :description="Str::limit($creator->bio,155)">
<div class="container section">
<a class="breadcrumb" href="{{ route('creators') }}">← Back to creators</a>
<div class="profile-cover">@if($creator->cover_path)<img src="{{ Storage::disk(config('filesystems.media_disk'))->url($creator->cover_path) }}" alt="{{ $creator->display_name }}’s cover">@else<span>{{ $creator->category->name }}</span>
<span>CREATOR PORTFOLIO</span>@endif</div>
<div class="profile-layout">
<div>
<div class="profile-identity">
<img class="profile-avatar" src="{{ Storage::disk(config('filesystems.media_disk'))->url($creator->photo_path) }}" alt="{{ $creator->display_name }}">
<div>
<span class="eyebrow">{{ $creator->location->name }}, {{ $creator->location->state }}</span>
<h1>{{ $creator->display_name }} @if($creator->verified_at)<span class="sr-only">Verified profile</span><x-market-icon name="shield"/>@endif</h1>
<span class="pill">{{ $creator->category->name }}</span>
</div>
</div>
<p class="profile-bio">{{ $creator->bio }}</p>
<div class="benefit-tags">@foreach($creator->categories as $category)<span>{{ $category->name }}</span>@endforeach @foreach($creator->languages as $language)<span>{{ $language->name }}</span>@endforeach</div>
<div class="social-list">@foreach($creator->socialAccounts as $social)<a class="social-account" href="{{ $social->url }}" target="_blank" rel="noopener noreferrer">
<strong>{{ ucfirst($social->platform) }} ↗</strong>@if($social->followers !== null)<span>{{ number_format($social->followers) }} followers</span>@endif@if($social->average_views !== null)<span>{{ number_format($social->average_views) }} average views</span>@endif<small>{{ $social->metrics_verified_at ? 'Metrics verified '.$social->metrics_verified_at->format('d M Y') : 'Self-reported · '.($social->metrics_reported_at?->format('d M Y') ?? 'date unavailable') }}</small>
</a>@endforeach</div>
<h2 class="subsection-title">Selected work.
</h2>
<div class="portfolio-grid">@foreach($creator->portfolioItems as $item)<x-portfolio-tile :item="$item" />@endforeach</div>
</div>
<aside class="hiring-panel" id="collaboration-services">
<span class="pill">{{ $creator->available ? '● Open for collaborations' : 'Currently unavailable' }}</span>
<h2>Create something together.
</h2>@foreach($creator->servicePackages as $package)<article class="package">
<div>
<h3>{{ $package->name }}</h3>
<strong>₹{{ number_format($package->price_minor/100,2) }}</strong>
</div>
<p>{{ $package->description }}</p>
<p>{{ $package->deliverables }}</p>
<small>{{ $package->turnaround_days }} day turnaround · {{ $package->revision_policy }}</small>
</article>@endforeach@if($creator->available)<a class="button button-purple" href="{{ auth()->check() ? route('collaborations',['creator'=>$creator->slug]) : route('register',['role'=>'brand']) }}">Send a collaboration brief <x-market-icon name="arrow-up-right"/>
</a>@endif<small class="payment-note">Payments are agreed and coordinated directly. Colliva does not provide escrow or payment guarantees.</small>
</aside>
</div>
@if($creator->available)<a class="button button-purple mobile-hire" href="#collaboration-services">View services &amp; collaborate <x-market-icon name="arrow-right" /></a>@endif
<livewire:report-creator :creator-id="$creator->id" />
</div>
</x-layouts::marketplace>
