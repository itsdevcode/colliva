@props(['creator', 'position', 'recommended' => true])
<article @class(['leaderboard-row', 'leaderboard-podium' => $recommended && $position <= 3]) aria-labelledby="leader-name-{{ $creator->id }}">
    <span class="leaderboard-rank" aria-label="{{ $recommended ? 'Leaderboard rank' : 'Sort position' }} {{ $position }}">{{ str_pad((string) $position, 2, '0', STR_PAD_LEFT) }}</span>
    <div class="leaderboard-identity">
        <a class="leaderboard-avatar" href="{{ route('creator.show', $creator->slug) }}" aria-label="View {{ $creator->display_name }}’s profile"><img src="{{ Storage::disk(config('filesystems.media_disk'))->url($creator->photo_path) }}" alt="{{ $creator->display_name }}" width="112" height="112" loading="lazy" decoding="async"></a>
        <div class="leaderboard-name"><h3 id="leader-name-{{ $creator->id }}"><a href="{{ route('creator.show', $creator->slug) }}">{{ $creator->display_name }}</a>@if($creator->verified_at)<span class="leaderboard-verified" title="Verified creator profile"><x-market-icon name="shield" /><span class="sr-only">Verified profile</span></span>@endif</h3><p>{{ '@'.$creator->slug }}</p><span class="leaderboard-category">{{ $creator->category->name }}</span></div>
    </div>
    <ul class="leaderboard-socials" aria-label="Platforms and audience">
        @forelse($creator->socialAccounts as $account)
            <li><x-market-icon :name="$account->platform === 'instagram' ? 'camera' : ($account->platform === 'youtube' ? 'play' : 'globe')" /><span>{{ ucfirst($account->platform) }}@if($account->followers !== null)<strong>{{ number_format($account->followers) }} followers</strong><small>{{ $account->metrics_verified_at ? 'Verified audience' : 'Creator-reported audience' }}</small>@endif</span></li>
        @empty
            <li><x-market-icon name="camera" /><span>Portfolio-led creator</span></li>
        @endforelse
    </ul>
    <span class="leaderboard-location"><x-market-icon name="pin" />{{ $creator->location->name }}</span>
    <span class="leaderboard-price"><small>Starting at</small>@if($creator->service_packages_min_price_minor !== null)<strong>₹{{ number_format($creator->service_packages_min_price_minor / 100) }}</strong>@else<span>Enquire</span>@endif</span>
    <x-colliva-button class="leaderboard-profile" :href="route('creator.show', $creator->slug)" variant="outline" :aria-label="'View '.$creator->display_name.'’s profile'">View Profile <x-market-icon name="arrow-up-right" /></x-colliva-button>
</article>
