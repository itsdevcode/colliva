<?php

use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\RoleLandingController;
use App\Http\Controllers\RoleRegistrationController;
use App\Http\Controllers\SupportArticleController;
use App\Http\Middleware\ValidateDiscoveryFilters;
use App\Livewire\BrandWorkspace;
use App\Livewire\CampaignBoard;
use App\Livewire\CollaborationWorkspace;
use App\Livewire\CreatorDiscovery;
use App\Livewire\CreatorOnboarding;
use App\Livewire\CustomerWorkspace;
use App\Livewire\Pricing;
use App\Livewire\SupportCenter;
use App\Livewire\SupportContact;
use App\Livewire\SupportTickets;
use App\Livewire\TopInfluencers;
use App\Models\CreatorProfile;
use Illuminate\Support\Facades\Route;

Route::livewire('/top-influencers', TopInfluencers::class)->middleware(ValidateDiscoveryFilters::class)->name('top-influencers');
Route::livewire('/top-influencers/{topic}', TopInfluencers::class)->where('topic', '[a-z0-9-]+')->middleware(ValidateDiscoveryFilters::class)->name('top-influencers.topic');
Route::livewire('/support', SupportCenter::class)->middleware(ValidateDiscoveryFilters::class)->name('support');
Route::livewire('/support/contact', SupportContact::class)->name('support.contact');
Route::get('/support/articles/{slug}', [SupportArticleController::class, 'show'])->name('support.article');
Route::middleware('auth')->group(function (): void {
    Route::livewire('/support/my-tickets', SupportTickets::class)->name('support.tickets');
    Route::livewire('/support/my-tickets/{reference}', SupportTickets::class)->name('support.ticket');
});
Route::livewire('/pricing', Pricing::class)->name('pricing');
Route::view('/get-started', 'get-started')->name('get-started');
foreach (['creator' => 'creators', 'brand' => 'brands', 'customer' => 'customers'] as $role => $plural) {
    Route::get('/for-'.$plural, [RoleLandingController::class, 'show'])->defaults('role', $role)->name('for-'.$plural);
}
Route::middleware('guest')->group(function (): void {
    Route::get('/signup/{role}', [RoleRegistrationController::class, 'create'])->whereIn('role', ['creator', 'brand', 'customer'])->name('signup');
    Route::post('/signup/{role}', [RoleRegistrationController::class, 'store'])->whereIn('role', ['creator', 'brand', 'customer'])->middleware('throttle:6,1')->name('signup.store');
});
Route::get('/', [MarketplaceController::class, 'index'])->name('home');
Route::livewire('/creators', CreatorDiscovery::class)->name('creators');
Route::livewire('/creators/{city}/{category?}', CreatorDiscovery::class)->name('creators.location');
Route::get('/creator/{slug}', [MarketplaceController::class, 'show'])->where('slug', '(?!dashboard$|onboarding$)[a-z0-9-]+')->name('creator.show');
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap', ['creators' => CreatorProfile::publiclyVisible()->select('slug', 'updated_at')->get()])->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route(auth()->user()->dashboardRoute()))->name('dashboard');
    foreach (['creator', 'brand'] as $role) {
        Route::get('/'.$role.'/dashboard', [MarketplaceController::class, 'dashboard'])->middleware('role:'.$role)->name($role.'.dashboard');
    }
    Route::livewire('/customer/dashboard', CustomerWorkspace::class)->middleware('role:customer')->name('customer.dashboard');
    Route::livewire('/creator/onboarding', CreatorOnboarding::class)->middleware('role:creator')->name('creator.onboarding');
    Route::livewire('/brand/onboarding', BrandWorkspace::class)->middleware('role:brand')->name('brand.onboarding');
    Route::livewire('/studio', CreatorOnboarding::class)->name('creator.studio');
    Route::livewire('/workspace', BrandWorkspace::class)->name('brand.workspace');
    Route::livewire('/campaigns', CampaignBoard::class)->name('campaigns');
    Route::livewire('/collaborations', CollaborationWorkspace::class)->name('collaborations');
});
require __DIR__.'/settings.php';
