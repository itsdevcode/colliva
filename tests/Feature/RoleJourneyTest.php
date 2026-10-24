<?php

use App\Actions\ManageCollaboration;
use App\Livewire\CollaborationWorkspace;
use App\Livewire\CreatorDiscovery;
use App\Livewire\CustomerWorkspace;
use App\Models\BrandProfile;
use App\Models\Category;
use App\Models\CollaborationRequest;
use App\Models\CreatorProfile;
use App\Models\CustomerProfile;
use App\Models\Location;
use App\Models\User;
use App\Notifications\MarketplaceUpdate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

test('role entry landing and signup pages render without role dropdowns', function (string $path) {
    $this->get($path)->assertOk()->assertDontSee('name="role"', false);
})->with(['/get-started', '/for-creators', '/for-brands', '/for-customers', '/signup/creator', '/signup/brand', '/signup/customer', '/login']);

test('dedicated signup creates only the selected profile and sends verification', function (string $role, string $table) {
    Notification::fake();
    $city = Location::factory()->create();
    $category = Category::factory()->create();

    $this->post(route('signup.store', $role), [
        'name' => 'New Member', 'email' => 'member@example.com', 'password' => 'password', 'password_confirmation' => 'password',
        'city' => $city->id, 'category' => $category->id, 'company_name' => 'Local Studio', 'business_category' => 'Retail',
        'email_verified_at' => now(), 'is_admin' => true,
    ])->assertSessionHasNoErrors()->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'member@example.com')->firstOrFail();
    expect($user->role)->toBe($role);
    expect($user->hasVerifiedEmail())->toBeFalse();
    $this->assertDatabaseHas($table, ['user_id' => $user->id]);
    foreach (array_diff(['creator_profiles', 'brand_profiles', 'customer_profiles'], [$table]) as $otherTable) {
        $this->assertDatabaseMissing($otherTable, ['user_id' => $user->id]);
    }
    Notification::assertSentTo($user, VerifyEmail::class);
    $this->assertAuthenticatedAs($user);
})->with([['creator', 'creator_profiles'], ['brand', 'brand_profiles'], ['customer', 'customer_profiles']]);

test('role injection cannot create an account through a dedicated signup', function (string $role) {
    $this->post(route('signup.store', 'customer'), ['name' => 'Attacker', 'email' => 'attacker@example.com', 'password' => 'password', 'password_confirmation' => 'password', 'role' => $role])->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('users', ['email' => 'attacker@example.com']);
})->with(['admin', 'brand', 'creator']);

test('public admin signup does not exist', function () {
    $this->get('/signup/admin')->assertNotFound();
    $this->post('/signup/admin')->assertNotFound();
});

test('role signup validates required profile details atomically', function (string $role, array $fields) {
    $this->post(route('signup.store', $role), [])->assertSessionHasErrors($fields);
    $this->assertDatabaseCount('users', 0);
})->with([
    ['creator', ['name', 'email', 'password', 'city', 'category']],
    ['brand', ['name', 'email', 'password', 'city', 'company_name', 'business_category']],
    ['customer', ['name', 'email', 'password']],
]);

test('signup rejects invalid profile references and mismatched passwords', function () {
    $this->post(route('signup.store', 'creator'), ['name' => 'Member', 'email' => 'member@example.com', 'password' => 'password', 'password_confirmation' => 'different', 'city' => 999, 'category' => 999])->assertSessionHasErrors(['password', 'city', 'category']);
    $this->assertDatabaseCount('users', 0);
});

test('existing email cannot register again or change its stored role', function () {
    $user = User::factory()->create(['role' => 'brand']);
    $this->post(route('signup.store', 'customer'), ['name' => 'Replacement', 'email' => $user->email, 'password' => 'password', 'password_confirmation' => 'password'])->assertSessionHasErrors('email');
    expect($user->fresh()->role)->toBe('brand');
    $this->assertDatabaseCount('users', 1);
});

test('login uses the stored role despite submitted roles and intended destinations', function (string $role, string $destination) {
    $user = User::factory()->create(['role' => $role]);
    $this->withSession(['url.intended' => route('brand.dashboard')])->post(route('login.store'), ['email' => $user->email, 'password' => 'password', 'role' => 'admin'])->assertRedirect(route($destination));
    $this->assertAuthenticatedAs($user);
})->with([['creator', 'creator.dashboard'], ['brand', 'brand.dashboard'], ['customer', 'customer.dashboard'], ['admin', 'filament.admin.pages.dashboard']]);

test('unverified accounts cannot enter any role dashboard', function (string $role) {
    $user = User::factory()->unverified()->create(['role' => $role]);
    $this->actingAs($user)->get(route($role.'.dashboard'))->assertRedirect(route('verification.notice'));
})->with(['creator', 'brand', 'customer']);

test('dashboard rejects every other account role', function (string $role, string $other) {
    $this->actingAs(User::factory()->create(['role' => $role]))->get(route($other.'.dashboard'))->assertForbidden();
})->with([['creator', 'brand'], ['creator', 'customer'], ['brand', 'creator'], ['brand', 'customer'], ['customer', 'creator'], ['customer', 'brand'], ['admin', 'customer']]);

test('incomplete creator and brand profiles enter onboarding', function (string $role) {
    $this->actingAs(User::factory()->create(['role' => $role]))->get(route($role.'.dashboard'))->assertRedirect(route($role.'.onboarding'));
})->with(['creator', 'brand']);

test('completed profiles can open their role dashboard', function (string $role) {
    $user = User::factory()->create(['role' => $role]);
    if ($role === 'creator') {
        CreatorProfile::factory()->for($user)->create(['bio' => 'My creative story.']);
    } else {
        BrandProfile::factory()->for($user)->create(['description' => 'Our business story.']);
    }
    $this->actingAs($user)->get(route($role.'.dashboard'))->assertOk();
})->with(['creator', 'brand']);

test('role dashboards and onboarding require authentication', function (string $path) {
    $this->get($path)->assertRedirect(route('login'));
})->with(['/creator/dashboard', '/brand/dashboard', '/customer/dashboard', '/creator/onboarding', '/brand/onboarding']);

test('customer can save creators and only remove their own shortlist entries', function () {
    $customer = CustomerProfile::factory()->create();
    $other = CustomerProfile::factory()->create();
    $creator = CreatorProfile::factory()->approved()->create();
    $other->user->savedCreators()->attach($creator);
    Livewire::actingAs($customer->user)->test(CreatorDiscovery::class)->call('saveCreator', $creator->id)->assertHasNoErrors();
    $this->actingAs($customer->user)->get(route('customer.dashboard'))->assertOk()->assertSee('Personal enquiries')->assertDontSee('Your brand workspace');
    Livewire::actingAs($customer->user)->test(CustomerWorkspace::class)->call('removeSaved', $creator->id);
    $this->assertDatabaseMissing('saved_creators', ['user_id' => $customer->user_id, 'creator_profile_id' => $creator->id]);
    $this->assertDatabaseHas('saved_creators', ['user_id' => $other->user_id, 'creator_profile_id' => $creator->id]);
});

test('customer can enquire collaborate and approve work without a brand profile', function () {
    Notification::fake();
    $this->freezeTime();
    $customer = CustomerProfile::factory()->create();
    $creator = CreatorProfile::factory()->approved()->create();
    Livewire::actingAs($customer->user)->test(CollaborationWorkspace::class)->set('creator', $creator->slug)->set('title', 'Personal celebration')->set('scope', 'Create an original short video of our celebration.')->set('price', '1500')->set('deadline', today()->addDays(10)->toDateString())->call('send')->assertHasNoErrors();
    $offer = CollaborationRequest::where('customer_profile_id', $customer->id)->firstOrFail();
    expect($offer->brand_profile_id)->toBeNull();
    $workflow = app(ManageCollaboration::class);
    $workflow->respond($creator->user, $offer->id, 'accepted');
    $workflow->submit($creator->user, $offer->fresh()->collaboration->id, 'https://example.com/work', 'Your original celebration video.');
    $workflow->review($customer->user, $offer->fresh()->collaboration->id, 'completed');
    $this->assertDatabaseHas('collaborations', ['collaboration_request_id' => $offer->id, 'status' => 'completed']);
    Notification::assertSentTo($customer->user, MarketplaceUpdate::class);
});

test('another customer cannot read or act on personal enquiries', function () {
    $owner = CustomerProfile::factory()->create();
    $other = CustomerProfile::factory()->create();
    $offer = CollaborationRequest::factory()->create(['brand_profile_id' => null, 'customer_profile_id' => $owner->id, 'title' => 'Private personal brief']);
    Livewire::actingAs($other->user)->test(CollaborationWorkspace::class)->assertDontSee('Private personal brief')->call('respond', $offer->id, 'cancelled')->assertNotFound();
    expect($offer->fresh()->status)->toBe('pending');
});

test('verified role accounts ignore stale intended pages and enter their own journey', function (string $role) {
    $this->freezeTime();
    $user = User::factory()->unverified()->create(['role' => $role]);
    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), ['id' => $user->id, 'hash' => sha1($user->email)]);

    $this->actingAs($user)->withSession(['url.intended' => route('brand.dashboard')])->get($url)->assertRedirect(route('dashboard', absolute: false).'?verified=1');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $this->get(route('dashboard'))->assertRedirect(route($role.'.dashboard'));
})->with(['creator', 'brand', 'customer']);

test('two factor recovery completes login into the stored role dashboard', function () {
    $user = User::factory()->withTwoFactor()->create(['role' => 'customer']);
    $this->withSession(['url.intended' => route('brand.dashboard')])->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('two-factor.login'));

    $this->post('/two-factor-challenge', ['recovery_code' => 'recovery-code-1'])->assertRedirect(route('customer.dashboard'));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->recoveryCodes())->not->toContain('recovery-code-1');
});

test('unverified login goes to verification rather than the stored dashboard', function () {
    $user = User::factory()->unverified()->create(['role' => 'customer']);
    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('verification.notice'));
});

test('creator signup produces a valid short profile slug for long and international names', function (string $name) {
    $this->post(route('signup.store', 'creator'), [
        'name' => $name, 'email' => 'member@example.com', 'password' => 'password', 'password_confirmation' => 'password',
        'city' => Location::factory()->create()->id, 'category' => Category::factory()->create()->id,
    ])->assertSessionHasNoErrors()->assertRedirect(route('verification.notice'));

    $profile = CreatorProfile::firstOrFail();
    expect($profile->slug)->toMatch('/^[a-z0-9]+(?:-[a-z0-9]+)*$/');
    expect(strlen($profile->slug))->toBeLessThanOrEqual(100);
})->with([str_repeat('A', 255), '王小明']);
