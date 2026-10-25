<?php

namespace App\Actions;

use App\Models\CreatorProfile;
use Illuminate\Database\Eloquent\Builder;

class RankCreators
{
    /** @return Builder<CreatorProfile> */
    public function query(string $platform = '', string $category = '', ?string $location = null, string $sort = 'recommended'): Builder
    {
        $signals = CreatorProfile::publiclyVisible()->select('creator_profiles.*')
            ->withCount(['portfolioItems as public_portfolio_count' => fn ($query) => $query->where('hidden', false), 'servicePackages as leaderboard_services_count'])
            ->withExists(['languages as leaderboard_has_languages', 'socialAccounts as leaderboard_has_socials'])
            ->withMax(['socialAccounts as leaderboard_verified_followers' => fn ($query) => $query->whereNotNull('metrics_verified_at')], 'followers')
            ->withMax('socialAccounts', 'followers')->withMin('servicePackages', 'price_minor');

        if (config('discovery.require_available')) {
            $signals->where('available', true);
        }
        if ($platform !== '') {
            $signals->whereHas('socialAccounts', fn ($query) => $query->where('platform', $platform));
        }
        if ($category !== '') {
            $signals->where(fn ($query) => $query->whereHas('category', fn ($related) => $related->where('slug', $category))
                ->orWhereHas('categories', fn ($related) => $related->where('slug', $category)));
        }
        if ($location !== null) {
            $signals->whereHas('location', fn ($query) => $query->where('slug', $location));
        }

        $portfolioCap = max(1, (int) config('leaderboard.portfolio_cap'));
        $serviceCap = max(1, (int) config('leaderboard.service_cap'));
        $audienceCap = max(1, (int) config('leaderboard.audience_cap'));
        $profilePoint = max(0, (int) config('leaderboard.weights.profile')) / 4;
        $score = "(CASE WHEN verified_at IS NOT NULL THEN CAST(? AS DECIMAL(12, 4)) ELSE 0 END)
            + (CASE WHEN bio IS NOT NULL AND TRIM(bio) <> '' THEN CAST(? AS DECIMAL(12, 4)) ELSE 0 END)
            + (CASE WHEN photo_path IS NOT NULL AND TRIM(photo_path) <> '' THEN CAST(? AS DECIMAL(12, 4)) ELSE 0 END)
            + (CASE WHEN leaderboard_has_languages THEN CAST(? AS DECIMAL(12, 4)) ELSE 0 END)
            + (CASE WHEN leaderboard_has_socials THEN CAST(? AS DECIMAL(12, 4)) ELSE 0 END)
            + (CASE WHEN public_portfolio_count >= ? THEN ? ELSE public_portfolio_count END) * 1.0 / ? * ?
            + (CASE WHEN leaderboard_services_count >= ? THEN ? ELSE leaderboard_services_count END) * 1.0 / ? * ?
            + (CASE WHEN updated_at >= ? AND updated_at <= ? THEN CAST(? AS DECIMAL(12, 4)) ELSE 0 END)
            + (CASE WHEN COALESCE(leaderboard_verified_followers, 0) >= ? THEN ?
                WHEN COALESCE(leaderboard_verified_followers, 0) < 0 THEN 0
                ELSE COALESCE(leaderboard_verified_followers, 0) END) * 1.0 / ? * ?";

        $query = CreatorProfile::query()->fromSub($signals->toBase(), 'creator_profiles')->select('creator_profiles.*')
            ->selectRaw('('.$score.') AS leaderboard_score', [
                max(0, (int) config('leaderboard.weights.verified')),
                $profilePoint, $profilePoint, $profilePoint, $profilePoint,
                $portfolioCap, $portfolioCap, $portfolioCap, max(0, (int) config('leaderboard.weights.portfolio')),
                $serviceCap, $serviceCap, $serviceCap, max(0, (int) config('leaderboard.weights.services')),
                now()->subDays(max(1, (int) config('leaderboard.activity_days'))), now(), max(0, (int) config('leaderboard.weights.activity')),
                $audienceCap, $audienceCap, $audienceCap, max(0, (int) config('leaderboard.weights.audience')),
            ])->withCasts(['leaderboard_score' => 'float'])->with(['location', 'category', 'socialAccounts']);

        match ($sort) {
            'followers' => $query->orderByRaw('COALESCE(social_accounts_max_followers, 0) DESC'),
            'active' => $query->orderByDesc('updated_at'),
            'price-asc' => $query->orderBy('service_packages_min_price_minor'),
            default => $query->orderByDesc('leaderboard_score'),
        };

        return $query->orderBy('id');
    }
}
