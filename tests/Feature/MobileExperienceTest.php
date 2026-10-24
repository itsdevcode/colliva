<?php

use App\Livewire\CampaignBoard;
use App\Livewire\CreatorDiscovery;
use App\Livewire\CreatorOnboarding;
use App\Models\BrandProfile;
use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Language;
use App\Models\Location;
use App\Models\User;
use Livewire\Livewire;

test('mobile filters leave applied results unchanged until apply and reopen restores applied values', function () {
    $city = Location::factory()->create();
    $component = Livewire::test(CreatorDiscovery::class)->set('city', $city->slug)->set('search', 'Portrait')->call('openFilters');

    $component->set('draftFilters.city', '')->set('draftFilters.available', true)->assertSet('city', $city->slug)->assertSet('available', false)->call('openFilters')->assertSet('draftFilters.city', $city->slug)->assertSet('draftFilters.available', false);

    $component->set('draftFilters.city', '')->set('draftFilters.available', true)->call('applyFilters')->assertHasNoErrors()->assertSet('city', '')->assertSet('available', true)->assertSet('search', 'Portrait')->assertDispatched('filters-applied');
});

test('mobile filter clear is staged and does not reset applied filters or pagination', function () {
    $city = Location::factory()->create();
    $component = Livewire::test(CreatorDiscovery::class)->set('city', $city->slug)->set('budget', '5000')->call('setPage', 2)->call('openFilters');

    $component->set('draftFilters.budget', '2000')->assertSet('paginators.page', 2)->call('clearDraftFilters')->assertSet('city', $city->slug)->assertSet('budget', '5000')->call('applyFilters')->assertSet('city', '')->assertSet('budget', '')->assertSet('paginators.page', 1);
});

test('invalid mobile filters cannot alter applied search state', function (string $field, mixed $value) {
    Livewire::test(CreatorDiscovery::class)->set('budget', '5000')->call('openFilters')->set('draftFilters.'.$field, $value)->call('applyFilters')->assertHasErrors(['draftFilters.'.$field])->assertSet('budget', '5000')->assertNotDispatched('filters-applied');
})->with([['city', 'unknown-city'], ['platform', 'invalid-platform'], ['budget', '-1'], ['minFollowers', 'not-a-number'], ['verified', 'admin']]);

test('mobile filter URLs initialize applied filters and draft input cannot assign other properties', function () {
    $category = Category::factory()->create();
    Livewire::withQueryParams(['category' => $category->slug, 'budget' => '2500'])->test(CreatorDiscovery::class)->assertSet('category', $category->slug)->assertSet('budget', '2500')->call('openFilters')->set('draftFilters.sort', 'malicious')->set('draftFilters.search', 'Injected')->call('applyFilters')->assertHasNoErrors()->assertSet('search', '')->assertSet('sort', 'recent');
});

test('campaign wizard validates the current step before allowing progress', function () {
    $brand = BrandProfile::factory()->create();
    Livewire::actingAs($brand->user)->test(CampaignBoard::class)->call('newCampaign')->call('nextCampaignStep')->assertSet('campaignStep', 1)->assertHasErrors(['title', 'description', 'category', 'budget'])->assertSee('aria-invalid="true"', false);
    $this->assertDatabaseCount('campaigns', 0);
});

test('campaign wizard retains earlier fields and saves through the existing draft action', function () {
    $this->freezeTime();
    $brand = BrandProfile::factory()->create();
    $category = Category::factory()->create();
    $city = Location::factory()->create();
    $component = Livewire::actingAs($brand->user)->test(CampaignBoard::class)->call('newCampaign')->set('title', 'A local launch')->set('description', 'An original collaboration with a local creator.')->set('category', (string) $category->id)->set('budget', '2500')->call('nextCampaignStep')->assertHasNoErrors()->assertSet('campaignStep', 2);

    $component->set('locations', [$city->id])->set('platforms', ['instagram'])->set('deliverables', 'One short original video')->set('requirements', 'Local lifestyle experience')->call('nextCampaignStep')->assertHasNoErrors()->assertSet('campaignStep', 3)->assertSet('title', 'A local launch')->set('applicationDeadline', today()->addDays(5)->toDateString())->set('deliveryDeadline', today()->addDays(10)->toDateString())->call('createCampaign', false)->assertHasNoErrors()->assertSet('formOpen', false)->assertSet('campaignStep', 1);

    $this->assertDatabaseHas('campaigns', ['brand_profile_id' => $brand->id, 'title' => 'A local launch', 'status' => 'draft', 'budget_minor' => 250000]);
});

test('campaign wizard cannot bypass final validation by changing its step', function () {
    $brand = BrandProfile::factory()->create();
    Livewire::actingAs($brand->user)->test(CampaignBoard::class)->set('campaignStep', 3)->call('createCampaign', true)->assertHasErrors(['title', 'locations', 'deliveryDeadline']);
    $this->assertDatabaseCount('campaigns', 0);
});

test('creators cannot use campaign wizard brand actions', function () {
    Livewire::actingAs(User::factory()->create())->test(CampaignBoard::class)->call('nextCampaignStep')->assertForbidden();
});

test('creator step navigation keeps entered fields within the current editing session', function () {
    $profile = CreatorProfile::factory()->create();
    Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)->set('bio', 'My original creative story in progress.')->set('step', 2)->set('step', 1)->assertSet('bio', 'My original creative story in progress.');
    expect($profile->fresh()->bio)->not->toBe('My original creative story in progress.');
});

test('role specific quick navigation has working destinations without exposing brand workflows to customers', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $this->actingAs($user)->get(route('customer.dashboard'))->assertOk()->assertSee('Customer quick navigation')->assertSee('href="'.route('collaborations').'#active-collaborations"', false)->assertDontSee('href="'.route('campaigns').'"', false);
    $this->get(route('collaborations'))->assertOk()->assertSee('id="enquiries"', false)->assertSee('id="active-collaborations"', false);
});

test('mobile navigation and profile service bar do not compete on public profiles', function () {
    $creator = CreatorProfile::factory()->approved()->create();
    $this->actingAs(User::factory()->create(['role' => 'customer']))->get(route('creator.show', $creator->slug))->assertOk()->assertSee('class="mobile-hire"', false)->assertDontSee('aria-label="Customer quick navigation"', false);
});

test('saving creator category details continues forward without losing the saved draft', function () {
    $profile = CreatorProfile::factory()->create();
    $language = Language::factory()->create();
    Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)->set('step', 3)->set('languages', [$language->id])->call('saveProfile')->assertHasNoErrors()->assertSet('step', 4);
    $this->assertDatabaseHas('creator_profiles', ['id' => $profile->id, 'bio' => $profile->bio, 'status' => 'draft']);
});
