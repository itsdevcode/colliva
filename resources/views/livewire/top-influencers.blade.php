<div class="container section leaderboard-page">
    <section class="leaderboard-intro" aria-labelledby="leaderboard-title">
        <span class="eyebrow">The Colliva creator leaderboard</span>
        <h1 id="leaderboard-title">Top creators on Colliva.</h1>
        <p>Discover standout influencers and UGC creators ranked for visibility, profile quality, and readiness to collaborate.</p>
        <details class="leaderboard-method" id="leaderboard-method">
            <summary>How this leaderboard works <x-market-icon name="arrow-right" /></summary>
            <div class="leaderboard-method-content">
                <p>Recommended ranks are calculated from real stored profile data. Only approved, published, non-demo creator profiles with a verified account email, photo, category, location, public portfolio and service package qualify.@if(config('discovery.require_available')) Creators must also be available for collaborations.@endif</p>
                <dl class="leaderboard-weights">
                    <div><dt>Verified creator profile</dt><dd>{{ config('leaderboard.weights.verified') }} points when staff has verified the profile.</dd></div>
                    <div><dt>Profile details</dt><dd>Up to {{ config('leaderboard.weights.profile') }} points, split equally between a biography, profile photo, a language and a social account.</dd></div>
                    <div><dt>Visible portfolio</dt><dd>Up to {{ config('leaderboard.weights.portfolio') }} points across {{ config('leaderboard.portfolio_cap') }} visible items. Hidden items do not count.</dd></div>
                    <div><dt>Collaboration services</dt><dd>Up to {{ config('leaderboard.weights.services') }} points across {{ config('leaderboard.service_cap') }} service packages.</dd></div>
                    <div><dt>Recent profile activity</dt><dd>{{ config('leaderboard.weights.activity') }} points for a profile updated within the past {{ config('leaderboard.activity_days') }} days. This is profile activity, not social engagement.</dd></div>
                    <div><dt>Verified audience</dt><dd>Up to {{ config('leaderboard.weights.audience') }} points, proportional to the largest staff-verified follower count on one account, capped at {{ number_format(config('leaderboard.audience_cap')) }}. Unverified or missing metrics earn no audience points.</dd></div>
                </dl>
                <p>Scores add up to {{ array_sum(config('leaderboard.weights')) }} possible points. Equal scores use the earlier profile ID first. Ranks reflect the selected platform, category and location. Other sort modes show positions in that selected order. Follower sorting may include creator-reported counts; these are labelled on each profile.</p>
                <p>This is a profile-readiness ranking, not a guarantee of performance or an objective measure of creative talent. No engagement scores or review ratings are used.</p>
            </div>
        </details>
    </section>

    <section id="leaderboard" class="leaderboard-board" aria-labelledby="leaderboard-heading">
        <div class="leaderboard-section-heading"><div><span class="eyebrow">Original people. Ready to create.</span><h2 id="leaderboard-heading">{{ $locationName ? 'The '.$locationName.' leaderboard' : 'Meet the leaderboard.' }}</h2></div><a class="text-link" href="{{ route('creators') }}">Discover all creators <x-market-icon name="arrow-up-right" /></a></div>
        <div class="leaderboard-controls">
            <div class="leaderboard-platforms" role="group" aria-label="Creator platform">
                @foreach(['' => 'All creators', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'ugc' => 'UGC creators'] as $value => $label)
                    <button type="button" wire:key="platform-{{ $value ?: 'all' }}" wire:click="$set('platform', '{{ $value }}')" @class(['is-active' => $platform === $value]) aria-pressed="{{ $platform === $value ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="leaderboard-selects">
                <x-filter-select model="category" label="Category" :value="$category" :options="$categories->pluck('name', 'slug')->all()" placeholder="All categories" />
                <x-filter-select model="sort" label="Order" :value="$activeSort" :options="['recommended' => 'Recommended', 'followers' => 'Most followers', 'active' => 'Recently active', 'price-asc' => 'Lowest starting price']" />
            </div>
        </div>
        <div class="leaderboard-status" role="status"><span wire:loading.remove>{{ $creators->total() }} {{ Str::plural('creator', $creators->total()) }}{{ $platform !== '' || $category !== '' ? ' in this selection' : ' on the leaderboard' }} · {{ $activeSort === 'recommended' ? 'Ranked by profile readiness' : 'Positions follow the selected order' }}</span><span wire:loading.delay>Updating the leaderboard…</span></div>
        <p class="leaderboard-offline" wire:offline role="alert">You’re offline. Reconnect to see the latest leaderboard.</p>
        <div class="leaderboard-loading" wire:loading.delay aria-hidden="true"><div></div><div></div><div></div></div>
        <div wire:loading.remove>
            @if($creators->isNotEmpty())
                <div class="leaderboard-column-headings" aria-hidden="true"><span>{{ $activeSort === 'recommended' ? 'Rank' : 'Position' }}</span><span>Creator</span><span>Platform &amp; audience</span><span>Location</span><span>Starting at</span><span class="sr-only">Profile link</span></div>
                <ol class="leaderboard-list" start="{{ $creators->firstItem() }}" aria-label="{{ $activeSort === 'recommended' ? 'Ranked creators' : 'Creators in selected order' }}">
                    @foreach($creators as $creator)
                        <li wire:key="leader-{{ $creator->id }}"><x-top-creator-row :creator="$creator" :position="$creators->firstItem() + $loop->index" :recommended="$activeSort === 'recommended'" /></li>
                    @endforeach
                </ol>
                <div class="leaderboard-pagination">{{ $creators->links() }}</div>
            @else
                <div class="leaderboard-empty">
                    <div class="leaderboard-empty-ranks" aria-hidden="true"><span>02</span><span>01</span><span>03</span></div>
                    @if($platform !== '' || $category !== '')
                        <h3>This selection is waiting for its first creators.</h3><p>Try another platform or category, or explore the full leaderboard.</p><x-colliva-button wire:click="resetControls" variant="outline">View all creators</x-colliva-button>
                    @else
                        <h3>The leaderboard is waiting for its first creators.</h3><p>We're building our creator community. Join now and be among the first to appear on Colliva's leaderboard.</p><x-colliva-button :href="route('signup', 'creator')">Join as Creator <x-market-icon name="arrow-right" /></x-colliva-button>
                    @endif
                </div>
            @endif
        </div>
    </section>
    <section class="leaderboard-invitation"><div><span class="eyebrow">Make your work discoverable</span><h2>Your next collaboration starts with your profile.</h2><p>Share your original work, add your services and let brands get to know you.</p></div><x-colliva-button :href="route('signup', 'creator')" variant="outline">Join as Creator <x-market-icon name="arrow-right" /></x-colliva-button></section>
</div>
