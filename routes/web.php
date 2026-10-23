<?php

use App\Http\Controllers\MarketplaceController;
use App\Livewire\BrandWorkspace;
use App\Livewire\CampaignBoard;
use App\Livewire\CollaborationWorkspace;
use App\Livewire\CreatorDiscovery;
use App\Livewire\CreatorOnboarding;
use App\Models\CreatorProfile;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'index'])->name('home');
Route::livewire('/creators', CreatorDiscovery::class)->name('creators');
Route::livewire('/creators/{city}/{category?}', CreatorDiscovery::class)->name('creators.location');
Route::get('/creator/{slug}', [MarketplaceController::class, 'show'])->name('creator.show');
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap', ['creators' => CreatorProfile::publiclyVisible()->select('slug', 'updated_at')->get()])->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [MarketplaceController::class, 'dashboard'])->name('dashboard');
    Route::livewire('/studio', CreatorOnboarding::class)->name('creator.studio');
    Route::livewire('/workspace', BrandWorkspace::class)->name('brand.workspace');
    Route::livewire('/campaigns', CampaignBoard::class)->name('campaigns');
    Route::livewire('/collaborations', CollaborationWorkspace::class)->name('collaborations');
});
require __DIR__.'/settings.php';
