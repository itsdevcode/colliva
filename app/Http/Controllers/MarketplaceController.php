<?php

namespace App\Http\Controllers;

use App\Models\CampaignApplication;
use App\Models\Category;
use App\Models\CollaborationRequest;
use App\Models\CreatorProfile;
use App\Models\Location;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(): View
    {
        return view('welcome', ['creators' => CreatorProfile::publiclyVisible()->with(['location', 'category', 'socialAccounts', 'portfolioItems' => fn ($q) => $q->where('hidden', false)])->withMin('servicePackages', 'price_minor')->latest('id')->limit(4)->get(), 'categories' => Category::orderBy('id')->get(), 'locations' => Location::orderBy('name')->get()]);
    }

    public function show(string $slug): View
    {
        $creator = CreatorProfile::publiclyVisible()->where('slug', $slug)->with(['location', 'category', 'categories', 'languages', 'socialAccounts', 'servicePackages', 'portfolioItems' => fn ($q) => $q->where('hidden', false)])->firstOrFail();

        return view('creator', compact('creator'));
    }

    public function dashboard(): View
    {
        $user = auth()->user();
        $profile = $user->role === 'creator' ? $user->creatorProfile : $user->brandProfile;

        $offers = CollaborationRequest::where(fn ($q) => $q->whereHas('brandProfile', fn ($b) => $b->where('user_id', $user->id))->orWhereHas('creatorProfile', fn ($c) => $c->where('user_id', $user->id)));
        $metrics = [
            'Pending enquiries' => (clone $offers)->where('status', 'pending')->count(),
            'Active collaborations' => (clone $offers)->whereHas('collaboration', fn ($q) => $q->whereIn('status', ['active', 'submitted', 'revision_requested']))->count(),
            'Completed collaborations' => (clone $offers)->whereHas('collaboration', fn ($q) => $q->where('status', 'completed'))->count(),
        ];
        if ($user->role === 'creator') {
            $metrics['Applications submitted'] = CampaignApplication::whereHas('creatorProfile', fn ($q) => $q->where('user_id', $user->id))->count();
        }

        return view('dashboard', compact('profile', 'metrics'));
    }
}
