<?php

use App\Actions\RankCreators;
use App\Livewire\TopInfluencers;
use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Language;
use App\Models\PortfolioItem;
use App\Models\ServicePackage;
use App\Models\SocialAccount;
use Livewire\Livewire;

test('top influencers loads without fabricated profiles', function () {
    $this->get(route('top-influencers'))->assertOk()->assertSee('Top creators on Colliva.')->assertSee('The leaderboard is waiting for its first creators.')->assertSee('How this leaderboard works')->assertDontSee('class="discovery-layout"', false)->assertDontSee('Filter creators')->assertDontSee('Name or username');
});
test('top influencers includes only approved published available real profiles', function () {
    $visible = CreatorProfile::factory()->approved()->create(['display_name' => 'Visible Creator']);
    CreatorProfile::factory()->approved()->create(['display_name' => 'Pending Creator', 'status' => 'pending']);
    CreatorProfile::factory()->approved()->create(['display_name' => 'Unpublished Creator', 'publication_consented_at' => null]);
    CreatorProfile::factory()->approved()->create(['display_name' => 'Demo Creator', 'is_demo' => true]);
    CreatorProfile::factory()->approved()->create(['display_name' => 'Unavailable Creator', 'available' => false]);
    $unverified = CreatorProfile::factory()->approved()->create(['display_name' => 'Unverified Email Creator']);
    $unverified->user->forceFill(['email_verified_at' => null])->save();
    $this->get(route('top-influencers'))->assertOk()->assertSee($visible->display_name)->assertSee('@'.$visible->slug)
        ->assertDontSee('Pending Creator')->assertDontSee('Unpublished Creator')->assertDontSee('Demo Creator')->assertDontSee('Unavailable Creator')->assertDontSee('Unverified Email Creator');
});
test('leaderboard platform tabs and category controls select matching approved creators', function () {
    $category = Category::factory()->create();
    $creator = CreatorProfile::factory()->approved()->create(['display_name' => 'Matching Creator', 'category_id' => $category->id]);
    SocialAccount::factory()->create(['creator_profile_id' => $creator->id, 'platform' => 'instagram', 'followers' => 12000]);
    $other = CreatorProfile::factory()->approved()->create(['display_name' => 'Other Creator']);
    SocialAccount::factory()->create(['creator_profile_id' => $other->id, 'platform' => 'youtube']);

    Livewire::test(TopInfluencers::class)->set('platform', 'instagram')->set('category', $category->slug)->assertSee('Matching Creator')->assertDontSee('Other Creator')
        ->set('platform', 'youtube')->assertSee('This selection is waiting for its first creators.')->call('resetControls')->assertSet('platform', '')->assertSet('category', '')->assertSee('Other Creator');
});

test('recommended leaderboard ranks actual profile readiness above unverified audience size', function () {
    $audience = CreatorProfile::factory()->approved()->create(['display_name' => 'Large Audience']);
    SocialAccount::factory()->create(['creator_profile_id' => $audience->id, 'followers' => 1000000]);
    $verified = CreatorProfile::factory()->approved()->create(['display_name' => 'Verified Partner', 'verified_at' => now()]);
    $complete = CreatorProfile::factory()->approved()->create(['display_name' => 'Complete Portfolio', 'verified_at' => now()]);
    PortfolioItem::factory()->create(['creator_profile_id' => $complete->id]);
    $this->get(route('top-influencers'))->assertOk()->assertSeeInOrder(['Complete Portfolio', 'Verified Partner', 'Large Audience']);
});
test('most followers recently active and starting price sorts use actual creator data', function () {
    $older = CreatorProfile::factory()->approved()->create(['display_name' => 'Older Affordable', 'created_at' => now()->subDays(2), 'updated_at' => now()->subDays(2)]);
    $newer = CreatorProfile::factory()->approved()->create(['display_name' => 'Newer Premium']);
    $older->servicePackages()->first()->update(['price_minor' => 100000]);
    $newer->servicePackages()->first()->update(['price_minor' => 900000]);
    SocialAccount::factory()->create(['creator_profile_id' => $older->id, 'followers' => 20000]);
    SocialAccount::factory()->create(['creator_profile_id' => $newer->id, 'followers' => 1000]);
    Livewire::test(TopInfluencers::class)->set('sort', 'followers')->assertSeeInOrder(['Older Affordable', 'Newer Premium'])
        ->set('sort', 'active')->assertSeeInOrder(['Newer Premium', 'Older Affordable'])
        ->set('sort', 'price-asc')->assertSeeInOrder(['Older Affordable', 'Newer Premium']);
});
test('top influencers paginates and handles unsafe filters without executing identifiers', function () {
    CreatorProfile::factory()->approved()->count(13)->create();
    Livewire::test(TopInfluencers::class)->assertViewHas('creators', fn ($creators) => $creators->count() === 12 && $creators->total() === 13)
        ->call('setPage', 2)->assertViewHas('creators', fn ($creators) => $creators->count() === 1)->assertSee('Leaderboard rank 13')
        ->set('sort', 'id; DROP TABLE users')->assertHasNoErrors()->assertSet('sort', 'recommended')->assertViewHas('activeSort', 'recommended')
        ->set('platform', 'unknown-platform')->assertSee('This selection is waiting for its first creators.')->call('resetControls')->assertSet('sort', 'recommended');
});
test('local discovery routes filter real locations and unknown topics return 404', function () {
    $creator = CreatorProfile::factory()->approved()->create(['display_name' => 'Local Partner']);
    $this->get(route('top-influencers.topic', $creator->location->slug))->assertOk()->assertSee('Local Partner')->assertSee('noindex,follow', false);
    $this->get(route('top-influencers.topic', 'nonexistent-topic'))->assertNotFound();
    $this->get(route('creator.show', $creator->slug))->assertOk();
});

test('top influencers rejects malformed filter arrays before hydration', function () {
    $this->get(route('top-influencers', ['city' => ['invalid']]))->assertBadRequest();
    $this->get(route('top-influencers', ['sort' => ['invalid']]))->assertBadRequest();
});

test('leaderboard rows distinguish verified and reported audiences and link to the real profile', function () {
    $creator = CreatorProfile::factory()->approved()->create(['display_name' => 'Original Work Creator']);
    SocialAccount::factory()->create(['creator_profile_id' => $creator->id, 'platform' => 'instagram', 'followers' => 12000, 'metrics_verified_at' => now()]);
    SocialAccount::factory()->create(['creator_profile_id' => $creator->id, 'platform' => 'youtube', 'followers' => 3000, 'metrics_verified_at' => null]);

    $this->get(route('top-influencers'))->assertOk()->assertSee('Original Work Creator')->assertSee('@'.$creator->slug)->assertSee($creator->category->name)
        ->assertSee($creator->location->name)->assertSee('12,000 followers')->assertSee('3,000 followers')->assertSee('Verified audience')->assertSee('Creator-reported audience')
        ->assertSee('href="'.route('creator.show', $creator->slug).'"', false)->assertSee('View Profile')->assertDontSee('Engagement score:');
    $this->get(route('creator.show', $creator->slug))->assertOk();
});

test('weighted leaderboard score uses real capped signals and excludes hidden portfolio work', function () {
    $this->freezeTime();
    $creator = CreatorProfile::factory()->approved()->create(['verified_at' => now()]);
    PortfolioItem::factory()->count(6)->create(['creator_profile_id' => $creator->id]);
    PortfolioItem::factory()->count(3)->create(['creator_profile_id' => $creator->id, 'hidden' => true]);
    ServicePackage::factory()->count(3)->create(['creator_profile_id' => $creator->id]);
    $creator->languages()->attach(Language::factory()->create());
    SocialAccount::factory()->create(['creator_profile_id' => $creator->id, 'platform' => 'instagram', 'followers' => 200000, 'metrics_verified_at' => now()]);

    $ranked = app(RankCreators::class)->query()->first();

    expect($ranked->leaderboard_score)->toBe(100.0);
    expect($ranked->public_portfolio_count)->toBe(7);
    $creator->socialAccounts()->update(['metrics_verified_at' => null]);
    expect(app(RankCreators::class)->query()->first()->leaderboard_score)->toBe(90.0);
    config(['leaderboard.weights.profile' => 18]);
    expect(app(RankCreators::class)->query()->first()->leaderboard_score)->toBe(88.0);
});

test('missing audience values and stale profile activity do not produce invented score points', function () {
    $this->freezeTime();
    $creator = CreatorProfile::factory()->approved()->create(['bio' => null, 'updated_at' => now()->subDays(31)]);
    PortfolioItem::factory()->create(['creator_profile_id' => $creator->id, 'hidden' => true]);
    SocialAccount::factory()->create(['creator_profile_id' => $creator->id, 'followers' => null, 'metrics_verified_at' => null]);

    $ranked = app(RankCreators::class)->query()->first();

    expect($ranked->leaderboard_score)->toBe(19.0);
    expect($ranked->public_portfolio_count)->toBe(1);
});

test('leaderboard weights are configurable and ties have a stable order', function () {
    config(['leaderboard.weights' => ['verified' => 0, 'profile' => 0, 'portfolio' => 100, 'services' => 0, 'activity' => 0, 'audience' => 0]]);
    $first = CreatorProfile::factory()->approved()->create(['display_name' => 'First Eligible Profile']);
    $second = CreatorProfile::factory()->approved()->create(['display_name' => 'Second Eligible Profile', 'verified_at' => now()]);
    $complete = CreatorProfile::factory()->approved()->create(['display_name' => 'More Visible Work']);
    PortfolioItem::factory()->create(['creator_profile_id' => $complete->id]);

    $this->get(route('top-influencers'))->assertOk()->assertSeeInOrder(['More Visible Work', 'First Eligible Profile', 'Second Eligible Profile']);
});

test('leaderboard platform selections persist in the URL and UGC uses actual social records', function () {
    $creator = CreatorProfile::factory()->approved()->create(['display_name' => 'Original UGC Partner']);
    SocialAccount::factory()->create(['creator_profile_id' => $creator->id, 'platform' => 'ugc', 'followers' => null]);
    CreatorProfile::factory()->approved()->create(['display_name' => 'Other Platform Partner']);

    $this->get(route('top-influencers', ['platform' => 'ugc']))->assertOk()->assertSee('Original UGC Partner')->assertDontSee('Other Platform Partner')->assertSee('Leaderboard rank 1');
    $this->get(route('creators'))->assertOk()->assertSee('Filter creators')->assertSee('Other Platform Partner')->assertDontSee('Top creators on Colliva.');
});
