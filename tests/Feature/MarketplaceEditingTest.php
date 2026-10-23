<?php

use App\Livewire\CampaignBoard;
use App\Livewire\CreatorOnboarding;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\CreatorProfile;
use App\Models\Language;
use App\Models\Location;
use App\Models\User;
use App\Notifications\MarketplaceUpdate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('brands edit draft campaigns without creating duplicates and replace targeting', function () {
    $campaign = Campaign::factory()->create();
    $originalCity = Location::factory()->create();
    $newCity = Location::factory()->create();
    $campaign->locations()->attach($originalCity);
    $campaign->platforms()->create(['platform' => 'instagram']);

    Livewire::actingAs($campaign->brandProfile->user)->test(CampaignBoard::class)
        ->call('editCampaign', $campaign->id)->assertSet('formOpen', true)
        ->set('title', 'Our updated local launch')
        ->set('budget', '7200.25')
        ->set('locations', [$newCity->id])->set('platforms', ['ugc'])
        ->call('createCampaign', false)->assertHasNoErrors()->assertSet('editingCampaignId', null);

    $this->assertDatabaseCount('campaigns', 1);
    $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'title' => 'Our updated local launch', 'budget_minor' => 720025, 'status' => 'draft']);
    $this->assertDatabaseHas('campaign_location', ['campaign_id' => $campaign->id, 'location_id' => $newCity->id]);
    $this->assertDatabaseMissing('campaign_location', ['campaign_id' => $campaign->id, 'location_id' => $originalCity->id]);
    $this->assertDatabaseHas('campaign_platforms', ['campaign_id' => $campaign->id, 'platform' => 'ugc']);
    $this->assertDatabaseMissing('campaign_platforms', ['campaign_id' => $campaign->id, 'platform' => 'instagram']);
});

test('edited campaign drafts can be submitted for moderation', function () {
    $campaign = Campaign::factory()->create();
    $city = Location::factory()->create();
    $campaign->locations()->attach($city);
    $campaign->platforms()->create(['platform' => 'instagram']);

    Livewire::actingAs($campaign->brandProfile->user)->test(CampaignBoard::class)
        ->call('editCampaign', $campaign->id)->set('title', 'Ready for review')
        ->call('createCampaign', true)->assertHasNoErrors();

    $this->assertDatabaseCount('campaigns', 1);
    $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'title' => 'Ready for review', 'status' => 'pending']);
});

test('brands cannot edit other brands campaign drafts', function () {
    $campaign = Campaign::factory()->create(['title' => 'Private draft']);
    $otherBrand = User::factory()->create(['role' => 'brand']);

    Livewire::actingAs($otherBrand)->test(CampaignBoard::class)
        ->call('editCampaign', $campaign->id)->assertForbidden();

    $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'title' => 'Private draft']);
});

test('campaigns cannot enter the editor after leaving draft', function (string $status) {
    $campaign = Campaign::factory()->create(['status' => $status]);

    Livewire::actingAs($campaign->brandProfile->user)->test(CampaignBoard::class)
        ->call('editCampaign', $campaign->id)->assertHasErrors('campaign')->assertSet('editingCampaignId', null);
})->with(['pending', 'published', 'closed', 'cancelled', 'completed']);

test('campaign saves recheck status when moderation starts while the editor is open', function () {
    $campaign = Campaign::factory()->create(['title' => 'Original brief']);
    $city = Location::factory()->create();
    $campaign->locations()->attach($city);
    $campaign->platforms()->create(['platform' => 'instagram']);
    $editor = Livewire::actingAs($campaign->brandProfile->user)->test(CampaignBoard::class)->call('editCampaign', $campaign->id);
    $campaign->forceFill(['status' => 'pending'])->save();

    $editor->set('title', 'Unreviewed change')->call('createCampaign', false)->assertHasErrors('campaign');

    $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'title' => 'Original brief', 'status' => 'pending']);
});

test('portfolio edits retain stored images and require renewed rights consent before resubmission', function () {
    $profile = CreatorProfile::factory()->approved()->create();
    $item = $profile->portfolioItems->first();
    $editor = Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)->call('editPortfolio', $item->id);

    $editor->set('portfolioTitle', 'A refreshed creative story')->call('addPortfolio')->assertHasErrors('rightsConfirmed');
    $this->assertDatabaseHas('creator_profiles', ['id' => $profile->id, 'status' => 'approved']);
    $editor->set('rightsConfirmed', true)->call('addPortfolio')->assertHasNoErrors()->assertSet('editingPortfolioId', null);

    $this->assertDatabaseCount('portfolio_items', 1);
    $this->assertDatabaseHas('portfolio_items', ['id' => $item->id, 'title' => 'A refreshed creative story', 'image_path' => $item->image_path]);
    $this->assertDatabaseHas('creator_profiles', ['id' => $profile->id, 'status' => 'draft']);
});

test('portfolio video edits cannot remove the only media', function () {
    $profile = CreatorProfile::factory()->create();
    $item = $profile->portfolioItems()->create(['title' => 'Original video', 'video_url' => 'https://example.com/video', 'rights_confirmed_at' => now()]);

    Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)
        ->call('editPortfolio', $item->id)->set('videoUrl', '')->set('rightsConfirmed', true)
        ->call('addPortfolio')->assertHasErrors(['portfolioImage', 'videoUrl']);

    $this->assertDatabaseHas('portfolio_items', ['id' => $item->id, 'video_url' => 'https://example.com/video']);
});

test('creators cannot edit another creators portfolio items', function () {
    $profile = CreatorProfile::factory()->create();
    $other = CreatorProfile::factory()->approved()->create();
    $item = $other->portfolioItems->first();

    Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)->call('editPortfolio', $item->id)->assertNotFound();

    $this->assertModelExists($item);
});

test('creators upload profile covers with raster validation', function () {
    Storage::fake('public');
    $profile = CreatorProfile::factory()->create();
    $language = Language::factory()->create();
    $profile->languages()->attach($language);

    $editor = Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)
        ->set('cover', UploadedFile::fake()->image('cover.jpg'))
        ->call('saveProfile')->assertHasNoErrors();

    Storage::disk('public')->assertExists($profile->refresh()->cover_path);
    $editor->set('cover', UploadedFile::fake()->create('cover.svg', 1, 'image/svg+xml'))
        ->call('saveProfile')->assertHasErrors('cover');
});

test('withdrawn applications can be resubmitted once with updated commercial terms', function () {
    Notification::fake();
    $profile = CreatorProfile::factory()->approved()->create();
    $campaign = Campaign::factory()->create(['status' => 'published', 'category_id' => $profile->category_id]);
    $campaign->locations()->attach($profile->location_id);
    $application = CampaignApplication::factory()->create(['campaign_id' => $campaign->id, 'creator_profile_id' => $profile->id]);

    Livewire::actingAs($profile->user)->test(CampaignBoard::class)
        ->call('withdraw', $application->id)->assertHasNoErrors()
        ->set('proposals.'.$campaign->id, 'A fresh local approach to your creative brief.')
        ->set('quotes.'.$campaign->id, '4200.50')
        ->call('apply', $campaign->id)->assertHasNoErrors()
        ->call('apply', $campaign->id)->assertHasErrors('proposals.'.$campaign->id);

    $this->assertDatabaseCount('campaign_applications', 1);
    $this->assertDatabaseHas('campaign_applications', ['id' => $application->id, 'status' => 'submitted', 'quote_minor' => 420050, 'proposal' => 'A fresh local approach to your creative brief.']);
    Notification::assertSentTo($campaign->brandProfile->user, MarketplaceUpdate::class);
});
