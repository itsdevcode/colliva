<?php

use App\Livewire\CreatorDiscovery;
use App\Models\Category;
use App\Models\CreatorProfile;
use App\Models\Language;
use App\Models\Location;
use App\Models\SocialAccount;
use Livewire\Livewire;

test('homepage renders the compact shared search without the old expanded selects', function () {
    $this->get(route('home'))->assertOk()->assertSee('All Filters')->assertSee('aria-label="Find creators"', false)->assertDontSee('Location &amp; category', false)->assertDontSee('<select', false);
});

test('each homepage filter produces the same matching discovery results', function (string $property, string|bool $value) {
    $city = Location::factory()->create(['slug' => 'yamunanagar']);
    $category = Category::factory()->create(['slug' => 'fashion']);
    $language = Language::factory()->create(['slug' => 'hindi']);
    $target = CreatorProfile::factory()->approved()->create(['display_name' => 'Target Creator', 'location_id' => $city->id, 'category_id' => $category->id, 'available' => true, 'verified_at' => now()]);
    $target->languages()->attach($language);
    SocialAccount::factory()->for($target)->create(['platform' => 'instagram', 'followers' => 5000]);
    $other = CreatorProfile::factory()->approved()->create(['display_name' => 'Other Creator', 'available' => false, 'verified_at' => null]);
    $other->servicePackages()->update(['price_minor' => 600000]);

    Livewire::test(CreatorDiscovery::class, ['compact' => true])->assertViewHas('resultCount', 2)->set($property, $value)->assertViewHas('resultCount', 1)->call('searchCreators')->assertRedirect(route('creators', [$property => $value]));

    $this->get(route('creators', [$property => $value]))->assertOk()->assertSee('Target Creator')->assertDontSee('Other Creator');
})->with([
    ['city', 'yamunanagar'], ['category', 'fashion'], ['platform', 'instagram'], ['budget', '5000'],
    ['minFollowers', '4000'], ['maxFollowers', '6000'], ['language', 'hindi'], ['available', true], ['verified', true], ['search', 'Target'],
]);

test('quick filter chips toggle their selected state and actual result count', function (string $preset, string $property, string|bool $value) {
    $fashion = Category::factory()->create(['slug' => 'fashion']);
    $beauty = Category::factory()->create(['slug' => 'beauty']);
    $target = CreatorProfile::factory()->approved()->create(['category_id' => $fashion->id, 'available' => true]);
    SocialAccount::factory()->for($target)->create(['platform' => 'ugc']);
    $other = CreatorProfile::factory()->approved()->create(['category_id' => $beauty->id, 'available' => false]);
    $other->servicePackages()->update(['price_minor' => 600000]);

    Livewire::test(CreatorDiscovery::class, ['compact' => true])->call('toggleQuickFilter', $preset)->assertSet($property, $value)->assertViewHas('resultCount', 1)->assertSee('aria-pressed="true"', false)->call('toggleQuickFilter', $preset)->assertSet($property, is_bool($value) ? false : '')->assertViewHas('resultCount', 2)->assertDontSee('aria-pressed="true"', false);
})->with([['fashion', 'category', 'fashion'], ['beauty', 'category', 'beauty'], ['ugc', 'platform', 'ugc'], ['budget', 'budget', '5000'], ['available', 'available', true]]);

test('homepage panel applies combined filters and preserves their query parameters on search', function () {
    $city = Location::factory()->create(['slug' => 'yamunanagar']);
    $category = Category::factory()->create(['slug' => 'fashion']);
    $target = CreatorProfile::factory()->approved()->create(['location_id' => $city->id, 'category_id' => $category->id]);
    CreatorProfile::factory()->approved()->create();

    Livewire::test(CreatorDiscovery::class, ['compact' => true])->set('search', $target->display_name)->call('openFilters')->set('draftFilters.city', 'yamunanagar')->set('draftFilters.category', 'fashion')->set('draftFilters.budget', '5000')->call('applyFilters')->assertHasNoErrors()->assertViewHas('resultCount', 1)->assertDispatched('filters-applied')->call('searchCreators')->assertRedirect(route('creators', ['search' => $target->display_name, 'city' => 'yamunanagar', 'category' => 'fashion', 'budget' => '5000']));
});

test('homepage clear all resets chips search and result count without including private profiles', function () {
    $category = Category::factory()->create(['slug' => 'fashion']);
    CreatorProfile::factory()->approved()->create(['category_id' => $category->id]);
    CreatorProfile::factory()->approved()->create();
    CreatorProfile::factory()->approved()->create(['status' => 'pending']);

    Livewire::test(CreatorDiscovery::class, ['compact' => true])->call('toggleQuickFilter', 'fashion')->assertViewHas('resultCount', 1)->call('clearFilters')->assertSet('category', '')->assertViewHas('resultCount', 2)->assertDontSee('aria-label="Active filters"', false)->call('searchCreators')->assertRedirect(route('creators'));
});

test('homepage query filters initialize the shared state and retain sort when navigating', function () {
    Livewire::withQueryParams(['platform' => 'ugc', 'budget' => '5000', 'sort' => 'price-asc'])->test(CreatorDiscovery::class, ['compact' => true])->assertSet('platform', 'ugc')->assertSet('budget', '5000')->assertSee('aria-pressed="true"', false)->call('searchCreators')->assertRedirect(route('creators', ['platform' => 'ugc', 'budget' => '5000', 'sort' => 'price-asc']));
});

test('unknown quick filter presets cannot mutate arbitrary component properties', function () {
    Livewire::test(CreatorDiscovery::class, ['compact' => true])->call('toggleQuickFilter', 'compact')->assertNotFound();
});
