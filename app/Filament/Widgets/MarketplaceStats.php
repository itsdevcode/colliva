<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\Collaboration;
use App\Models\CreatorProfile;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarketplaceStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Registered creators', User::where('role', 'creator')->count()),
            Stat::make('Approved creators', CreatorProfile::where('status', 'approved')->count()),
            Stat::make('Registered brands', User::where('role', 'brand')->count()),
            Stat::make('Published campaigns', Campaign::where('status', 'published')->count()),
            Stat::make('Pending approvals', CreatorProfile::where('status', 'pending')->count()),
            Stat::make('Completed collaborations', Collaboration::where('status', 'completed')->count()),
        ];
    }
}
