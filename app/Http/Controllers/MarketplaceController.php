<?php
namespace App\Http\Controllers;
use App\Models\{Category, CreatorProfile, Location};
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
class MarketplaceController extends Controller
{
 public function index(): View { return view('welcome', ['creators' => CreatorProfile::publiclyVisible()->with(['location', 'category', 'socialAccounts', 'portfolioItems' => fn ($q) => $q->where('hidden', false)])->withMin('servicePackages', 'price_minor')->latest('id')->limit(4)->get(), 'categories' => Category::orderBy('id')->get(), 'locations' => Location::orderBy('name')->get()]); }
 public function show(string $slug): View { $creator = CreatorProfile::publiclyVisible()->where('slug', $slug)->with(['location', 'category', 'languages', 'socialAccounts', 'servicePackages', 'portfolioItems' => fn ($q) => $q->where('hidden', false)])->firstOrFail(); return view('creator', compact('creator')); }
 public function dashboard(): View { $user = auth()->user(); $profile = $user->role === 'creator' ? $user->creatorProfile : $user->brandProfile; return view('dashboard', compact('profile')); }
}
