<?php

use App\Actions\ManageCollaboration;
use App\Actions\ModerateCreator;
use App\Filament\Resources\Campaigns\Pages\ManageCampaigns;
use App\Livewire\BrandWorkspace;
use App\Livewire\CampaignBoard;
use App\Livewire\CollaborationWorkspace;
use App\Livewire\CreatorDiscovery;
use App\Livewire\CreatorOnboarding;
use App\Livewire\ReportCreator;
use App\Models\BrandProfile;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Category;
use App\Models\Collaboration;
use App\Models\CollaborationRequest;
use App\Models\CreatorProfile;
use App\Models\Language;
use App\Models\Location;
use App\Models\ServicePackage;
use App\Models\User;
use App\Notifications\MarketplaceUpdate;
use Database\Seeders\MarketplaceSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

test('public pages render without fabricated listings', function () {
    $this->seed(MarketplaceSeeder::class);
    $this->get(route('home'))->assertOk()->assertSee('The first voices')->assertSee('Yamunanagar');
    $this->get(route('creators'))->assertOk()->assertSee('No creators here just yet');
    $this->get(route('sitemap'))->assertOk()->assertHeader('Content-Type', 'application/xml');
});

test('registration accepts creator and brand roles and rejects admin escalation', function (string $role, bool $allowed) {
    Notification::fake();
    $response = $this->post(route('register.store'), ['name' => 'New Member', 'email' => 'new@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'role' => $role, 'city' => Location::factory()->create()->id, 'category' => Category::factory()->create()->id, 'company_name' => 'Local Business', 'business_category' => 'Retail']);
    if ($allowed) {
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'new@example.com', 'role' => $role]);
        Notification::assertSentTo(User::where('email', 'new@example.com')->first(), VerifyEmail::class);
    } else {
        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);
        Notification::assertNothingSent();
    }
})->with([['creator', true], ['brand', true], ['admin', false]]);

test('workspace routes require verified accounts and the appropriate role', function () {
    $creator = User::factory()->create();
    $brand = User::factory()->create(['role' => 'brand']);
    $unverified = User::factory()->unverified()->create();
    $this->get(route('creator.studio'))->assertRedirect(route('login'));
    $this->actingAs($unverified)->get(route('creator.studio'))->assertRedirect(route('verification.notice'));
    $this->actingAs($brand)->get(route('creator.studio'))->assertForbidden();
    $this->actingAs($creator)->get(route('brand.workspace'))->assertForbidden();
    $this->actingAs($creator)->get(route('creator.studio'))->assertOk();
    $this->actingAs($brand)->get(route('brand.workspace'))->assertOk();
    $this->actingAs($creator)->get(route('dashboard'))->assertRedirect(route('creator.dashboard'));
});

test('discovery filters actual approved consenting profiles and excludes hidden records', function () {
    $city = Location::factory()->create(['slug' => 'yamunanagar']);
    $category = Category::factory()->create(['slug' => 'food']);
    $visible = CreatorProfile::factory()->approved()->create(['display_name' => 'Visible Creator', 'location_id' => $city->id, 'category_id' => $category->id]);
    $hidden = CreatorProfile::factory()->approved()->create(['display_name' => 'Hidden Creator', 'status' => 'pending']);
    $unpublished = CreatorProfile::factory()->approved()->create(['display_name' => 'Unpublished Creator', 'publication_consented_at' => null]);
    $other = CreatorProfile::factory()->approved()->create(['display_name' => 'Other City']);
    Livewire::test(CreatorDiscovery::class)->set('city', 'yamunanagar')->set('category', 'food')->set('budget', '3000')->assertSee('Visible Creator')->assertDontSee('Hidden Creator')->assertDontSee('Unpublished Creator')->assertDontSee('Other City')->set('budget', '100')->assertDontSee('Visible Creator');
    $this->get(route('creator.show', $hidden->slug))->assertNotFound();
    $this->get(route('creator.show', $unpublished->slug))->assertNotFound();
    $this->get(route('creator.show', $visible->slug))->assertOk()->assertSee('Visible Creator');
});

test('discovery supports platforms followers languages availability verification sorting and pagination', function () {
    $language = Language::factory()->create(['slug' => 'hindi']);
    $profile = CreatorProfile::factory()->approved()->create(['display_name' => 'Target Creator', 'verified_at' => now()]);
    $profile->languages()->attach($language);
    $profile->socialAccounts()->create(['platform' => 'instagram', 'url' => 'https://instagram.com/example', 'followers' => 5000, 'metrics_reported_at' => now()]);
    CreatorProfile::factory()->approved()->count(12)->create();
    Livewire::test(CreatorDiscovery::class)->assertDontSee('Target Creator')->call('nextPage')->assertSee('Target Creator');
    Livewire::test(CreatorDiscovery::class)->set('platform', 'instagram')->set('language', 'hindi')->set('minFollowers', '4000')->set('maxFollowers', '6000')->set('available', true)->set('verified', true)->set('sort', 'price-asc')->assertSee('Target Creator')->set('minFollowers', '6000')->assertDontSee('Target Creator')->call('clearFilters')->assertSet('minFollowers', '');
});

test('creator completes onboarding with safe media and submits consent for approval', function () {
    Storage::fake('public');
    Notification::fake();
    $user = User::factory()->create();
    $city = Location::factory()->create();
    $category = Category::factory()->create();
    $language = Language::factory()->create();
    Livewire::actingAs($user)->test(CreatorOnboarding::class)->set('displayName', 'My Creative Name')->set('slug', 'creative-name')->set('bio', 'Original local stories.')->set('city', (string) $city->id)->set('category', (string) $category->id)->set('languages', [$language->id])->set('photo', UploadedFile::fake()->image('avatar.jpg'))->call('saveProfile')->assertHasNoErrors()->set('socialUrl', 'https://instagram.com/creative')->set('followers', 1000)->call('saveSocial')->assertHasNoErrors()->set('portfolioTitle', 'A local story')->set('portfolioImage', UploadedFile::fake()->image('story.jpg'))->set('rightsConfirmed', true)->call('addPortfolio')->assertHasNoErrors()->set('packageName', 'Restaurant reel')->set('packageDescription', 'An original restaurant story.')->set('packagePrice', '2500.50')->set('deliverables', 'One vertical video')->call('addPackage')->assertHasNoErrors()->set('publicationConsent', true)->call('submit')->assertHasNoErrors();
    $this->assertDatabaseHas('creator_profiles', ['user_id' => $user->id, 'status' => 'pending']);
    $this->assertDatabaseHas('service_packages', ['price_minor' => 250050]);
    $profile = $user->creatorProfile()->first();
    Storage::disk('public')->assertExists($profile->photo_path);
    app(ModerateCreator::class)->handle(User::factory()->create(['role' => 'admin']), $profile, 'approved');
    $this->get(route('creator.show', 'creative-name'))->assertOk()->assertSee('Self-reported');
    Notification::assertSentTo($user, MarketplaceUpdate::class);
});

test('creator cannot publish incomplete profiles or upload active content', function () {
    $profile = CreatorProfile::factory()->create();
    Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)->call('submit')->assertHasErrors('publicationConsent')->set('publicationConsent', true)->call('submit')->assertHasErrors('publicationConsent')->set('portfolioTitle', 'Unsafe')->set('portfolioImage', UploadedFile::fake()->create('image.svg', 1, 'image/svg+xml'))->set('rightsConfirmed', true)->call('addPortfolio')->assertHasErrors('portfolioImage');
    $this->assertDatabaseHas('creator_profiles', ['id' => $profile->id, 'status' => 'draft']);
});

test('creators cannot remove another creators packages and portfolios', function () {
    $own = CreatorProfile::factory()->create();
    $other = CreatorProfile::factory()->approved()->create();
    $package = $other->servicePackages->first();
    $item = $other->portfolioItems->first();
    Livewire::actingAs($own->user)->test(CreatorOnboarding::class)->call('deletePackage', $package->id)->assertNotFound();
    Livewire::actingAs($own->user)->test(CreatorOnboarding::class)->call('deletePortfolio', $item->id)->assertNotFound();
    $this->assertModelExists($package);
    $this->assertModelExists($item);
});

test('creator can unpublish and edits require another review', function () {
    $profile = CreatorProfile::factory()->approved()->create();
    $language = Language::factory()->create();
    $profile->languages()->attach($language);
    Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)->call('unpublish')->assertHasNoErrors();
    $this->get(route('creator.show', $profile->slug))->assertNotFound();
    $this->assertDatabaseHas('creator_profiles', ['id' => $profile->id, 'status' => 'draft', 'publication_consented_at' => null]);
});

test('brands save business profiles and shortlist only publicly visible creators', function () {
    $brand = User::factory()->create(['role' => 'brand']);
    $city = Location::factory()->create();
    $profile = CreatorProfile::factory()->approved()->create();
    Livewire::actingAs($brand)->test(BrandWorkspace::class)->set('companyName', 'Local Business')->set('description', 'We make things locally.')->set('city', (string) $city->id)->call('save')->assertHasNoErrors();
    Livewire::actingAs($brand)->test(CreatorDiscovery::class)->call('saveCreator', $profile->id)->assertHasNoErrors();
    $this->assertDatabaseHas('brand_profiles', ['user_id' => $brand->id, 'company_name' => 'Local Business']);
    $this->assertDatabaseHas('saved_creators', ['user_id' => $brand->id, 'creator_profile_id' => $profile->id]);
});

test('brand creates moderated campaigns and another brand cannot change them', function () {
    $brand = BrandProfile::factory()->create();
    $city = Location::factory()->create();
    $category = Category::factory()->create();
    Livewire::actingAs($brand->user)->test(CampaignBoard::class)->set('title', 'New restaurant launch')->set('description', 'Tell the story of our new restaurant.')->set('category', (string) $category->id)->set('locations', [$city->id])->set('platforms', ['instagram'])->set('deliverables', 'One original reel')->set('requirements', 'A local food creator')->set('budget', '5000')->set('applicationDeadline', today()->addDays(7)->toDateString())->set('deliveryDeadline', today()->addDays(14)->toDateString())->call('createCampaign', true)->assertHasNoErrors();
    $campaign = Campaign::first();
    $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'status' => 'pending', 'budget_minor' => 500000]);
    Livewire::actingAs(User::factory()->create(['role' => 'brand']))->test(CampaignBoard::class)->call('changeStatus', $campaign->id, 'cancelled')->assertForbidden();
});

test('eligible creators can apply once and brands can accept with creator confirmation required', function () {
    Notification::fake();
    $profile = CreatorProfile::factory()->approved()->create();
    $campaign = Campaign::factory()->create(['status' => 'published', 'category_id' => $profile->category_id]);
    $campaign->locations()->attach($profile->location_id);
    Livewire::actingAs($profile->user)->test(CampaignBoard::class)->set('proposals.'.$campaign->id, 'I will create an original local story for this campaign.')->set('quotes.'.$campaign->id, '3000')->call('apply', $campaign->id)->assertHasNoErrors()->call('apply', $campaign->id)->assertHasErrors('proposals.'.$campaign->id);
    $application = CampaignApplication::first();
    Livewire::actingAs($campaign->brandProfile->user)->test(CampaignBoard::class)->call('review', $application->id, 'accepted')->assertHasNoErrors();
    $this->assertDatabaseHas('campaign_applications', ['id' => $application->id, 'status' => 'accepted']);
    $offer = CollaborationRequest::first();
    $this->assertDatabaseCount('collaborations', 0);
    app(ManageCollaboration::class)->respond($profile->user, $offer->id, 'accepted');
    $this->assertDatabaseHas('collaborations', ['collaboration_request_id' => $offer->id, 'status' => 'active']);
    Notification::assertSentTo($profile->user, MarketplaceUpdate::class);
});

test('creators cannot apply outside campaign eligibility or deadlines', function () {
    $profile = CreatorProfile::factory()->approved()->create();
    $campaign = Campaign::factory()->create(['status' => 'published']);
    Livewire::actingAs($profile->user)->test(CampaignBoard::class)->set('proposals.'.$campaign->id, 'An original proposal for a local story.')->set('quotes.'.$campaign->id, '3000')->call('apply', $campaign->id)->assertForbidden();
    $this->assertDatabaseCount('campaign_applications', 0);
});

test('brand sends an enquiry and creator acceptance creates a collaboration', function () {
    Notification::fake();
    $brand = BrandProfile::factory()->create();
    $creator = CreatorProfile::factory()->approved()->create();
    Livewire::actingAs($brand->user)->test(CollaborationWorkspace::class)->set('creator', $creator->slug)->set('title', 'Our next brand story')->set('scope', 'One original reel with direct payment upon delivery.')->set('price', '3500')->set('deadline', today()->addDays(10)->toDateString())->call('send')->assertHasNoErrors();
    $offer = CollaborationRequest::first();
    $this->assertDatabaseHas('collaboration_requests', ['id' => $offer->id, 'status' => 'pending', 'price_minor' => 350000]);
    app(ManageCollaboration::class)->respond($creator->user, $offer->id, 'accepted');
    $this->assertDatabaseHas('collaborations', ['collaboration_request_id' => $offer->id, 'status' => 'active']);
    Notification::assertSentTo($creator->user, MarketplaceUpdate::class);
});

test('collaborations support delivery revision and completion and reject invalid transitions', function () {
    Notification::fake();
    $offer = CollaborationRequest::factory()->create();
    $workflow = app(ManageCollaboration::class);
    $creator = $offer->creatorProfile->user;
    $brand = $offer->brandProfile->user;
    $workflow->respond($creator, $offer->id, 'accepted');
    $collaboration = Collaboration::first();
    $workflow->submit($creator, $collaboration->id, 'https://example.com/work', 'First draft');
    $workflow->review($brand, $collaboration->id, 'revision_requested');
    $workflow->submit($creator, $collaboration->id, 'https://example.com/final', 'Final version');
    $workflow->review($brand, $collaboration->id, 'completed');
    $this->assertDatabaseHas('collaborations', ['id' => $collaboration->id, 'status' => 'completed']);
    $this->assertDatabaseCount('collaboration_deliverables', 2);
    expect(fn () => $workflow->submit($creator, $collaboration->id, 'https://example.com/extra', 'Too late'))->toThrow(ValidationException::class);
    Notification::assertSentTo($creator, MarketplaceUpdate::class);
});

test('briefs can be negotiated before acceptance', function () {
    Notification::fake();
    $offer = CollaborationRequest::factory()->create();
    $workflow = app(ManageCollaboration::class);
    $workflow->respond($offer->creatorProfile->user, $offer->id, 'changes_requested', 'Please allow two more days.');
    $workflow->revise($offer->brandProfile->user, $offer->id, 'One reel, direct payment.', 400000, today()->addDays(15)->toDateString());
    $workflow->respond($offer->creatorProfile->user, $offer->id, 'accepted');
    $this->assertDatabaseHas('collaboration_requests', ['id' => $offer->id, 'status' => 'accepted', 'price_minor' => 400000]);
    $this->assertDatabaseCount('collaborations', 1);
    Notification::assertSentTo($offer->creatorProfile->user, MarketplaceUpdate::class);
});

test('private briefs and deliverables are isolated from other users', function () {
    $offer = CollaborationRequest::factory()->create(['title' => 'Private brand brief']);
    $stranger = User::factory()->create();
    Livewire::actingAs($stranger)->test(CollaborationWorkspace::class)->assertDontSee('Private brand brief')->call('respond', $offer->id, 'accepted')->assertNotFound();
    $this->assertDatabaseCount('collaborations', 0);
});

test('public profiles escape creator supplied text and unsafe sort strings do not execute', function () {
    $profile = CreatorProfile::factory()->approved()->create(['display_name' => '<script>alert(1)</script>', 'bio' => '<img src=x onerror=alert(1)>']);
    $this->get(route('creator.show', $profile->slug))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;img', false);
    Livewire::test(CreatorDiscovery::class)->set('sort', 'price; DROP TABLE users')->assertSee('&lt;script&gt;', false);
    $this->assertModelExists($profile);
});

test('admin panel rejects regular users and exposes moderation to verified admins', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user)->get('/admin')->assertForbidden();
    $this->actingAs($admin)->get('/admin')->assertOk();
    $this->actingAs($admin)->get('/admin/creator-profiles')->assertOk();
    $this->actingAs($admin)->get('/admin/campaigns')->assertOk();
});

test('creator moderation requires admin authority and complete publication consent', function () {
    Notification::fake();
    $profile = CreatorProfile::factory()->approved()->create(['status' => 'pending']);
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($profile->user);
    expect(fn () => app(ModerateCreator::class)->handle($profile->user, $profile, 'approved'))->toThrow(AuthorizationException::class);
    app(ModerateCreator::class)->handle($admin, $profile, 'approved');
    $this->assertDatabaseHas('creator_profiles', ['id' => $profile->id, 'status' => 'approved']);
    $this->assertDatabaseHas('audit_logs', ['subject_id' => $profile->id, 'action' => 'creator.approved']);
    Notification::assertSentTo($profile->user, MarketplaceUpdate::class);
});

test('admin campaign moderation publishes a pending campaign and records the decision', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $campaign = Campaign::factory()->create(['status' => 'pending']);
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::actingAs($admin)->test(ManageCampaigns::class)
        ->callAction(TestAction::make('approve')->table($campaign))
        ->assertHasNoActionErrors();

    $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'status' => 'published']);
    $this->assertDatabaseHas('audit_logs', ['subject_id' => $campaign->id, 'action' => 'campaign.published']);
});

test('creators edit their service packages and the profile returns to draft', function () {
    $profile = CreatorProfile::factory()->approved()->create();
    $package = $profile->servicePackages->first();

    Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)
        ->call('editPackage', $package->id)
        ->set('packagePrice', '3500.25')
        ->call('addPackage')->assertHasNoErrors();

    $this->assertDatabaseCount('service_packages', 1);
    $this->assertDatabaseHas('service_packages', ['id' => $package->id, 'price_minor' => 350025]);
    $this->assertDatabaseHas('creator_profiles', ['id' => $profile->id, 'status' => 'draft']);
});

test('creators cannot edit another creators service package', function () {
    $profile = CreatorProfile::factory()->create();
    $other = CreatorProfile::factory()->approved()->create();
    $package = $other->servicePackages->first();

    Livewire::actingAs($profile->user)->test(CreatorOnboarding::class)->call('editPackage', $package->id)->assertNotFound();

    $this->assertDatabaseHas('service_packages', ['id' => $package->id, 'price_minor' => 250000]);
});

test('verified members can report profiles without changing their publication state', function () {
    $profile = CreatorProfile::factory()->approved()->create();
    $user = User::factory()->create(['role' => 'brand']);

    Livewire::actingAs($user)->test(ReportCreator::class, ['creatorId' => $profile->id])
        ->set('reason', 'Please review the ownership of the selected portfolio image.')
        ->call('send')->assertHasNoErrors()->assertSet('sent', true);

    $this->assertDatabaseHas('content_reports', ['user_id' => $user->id, 'creator_profile_id' => $profile->id, 'status' => 'open']);
    $this->assertDatabaseHas('creator_profiles', ['id' => $profile->id, 'status' => 'approved']);
});

test('brands and unrelated creators cannot submit or approve another collaboration', function () {
    $collaboration = Collaboration::factory()->create();
    $other = User::factory()->create();
    $workflow = app(ManageCollaboration::class);

    expect(fn () => $workflow->submit($other, $collaboration->id, 'https://example.com/work', 'Private work'))
        ->toThrow(AuthorizationException::class);
    expect(fn () => $workflow->review($other, $collaboration->id, 'completed'))
        ->toThrow(AuthorizationException::class);
    $this->assertDatabaseCount('collaboration_deliverables', 0);
    $this->assertDatabaseHas('collaborations', ['id' => $collaboration->id, 'status' => 'active']);
});

test('brand cannot complete a campaign before accepted collaborations are complete', function () {
    $campaign = Campaign::factory()->create(['status' => 'closed']);
    $application = CampaignApplication::factory()->create(['campaign_id' => $campaign->id, 'status' => 'accepted']);
    $offer = CollaborationRequest::factory()->create(['campaign_application_id' => $application->id, 'brand_profile_id' => $campaign->brand_profile_id, 'creator_profile_id' => $application->creator_profile_id, 'status' => 'accepted', 'creator_confirmed_at' => now()]);
    Collaboration::factory()->create(['collaboration_request_id' => $offer->id]);

    Livewire::actingAs($campaign->brandProfile->user)->test(CampaignBoard::class)
        ->call('changeStatus', $campaign->id, 'completed')->assertHasErrors('campaign');

    $this->assertDatabaseHas('campaigns', ['id' => $campaign->id, 'status' => 'closed']);
});

test('category links retain category filters independently of city landing routes', function () {
    $category = Category::factory()->create(['name' => 'Food', 'slug' => 'food']);
    $creator = CreatorProfile::factory()->approved()->create(['display_name' => 'Local Food Creator', 'category_id' => $category->id]);

    $this->get(route('creators', ['category' => 'food']))->assertOk()->assertSee('Local Food Creator');
    $this->get(route('creators.location', ['city' => $creator->location->slug, 'category' => 'food']))->assertOk()->assertSee('Local Food Creator');
});

test('postgres rejects nonpositive service prices at the database boundary', function () {
    if (DB::getDriverName() !== 'pgsql') {
        $this->markTestSkipped('This integrity constraint is specific to PostgreSQL.');
    }
    $profile = CreatorProfile::factory()->create();

    expect(fn () => ServicePackage::factory()->create(['creator_profile_id' => $profile->id, 'price_minor' => 0]))
        ->toThrow(QueryException::class);
});

test('notification emails escape user supplied brief titles', function () {
    $user = User::factory()->make();
    $notification = new MarketplaceUpdate('Your brief <script>alert(1)</script> was accepted.');

    $html = $notification->toMail($user)->render()->toHtml();

    expect($html)->toContain('&lt;script&gt;')->not->toContain('<script>alert(1)</script>');
});
