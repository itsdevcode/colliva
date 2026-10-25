<?php

use App\Actions\ManageSupportTicket;
use App\Filament\Resources\SupportArticles\Pages\ManageSupportArticles;
use App\Filament\Resources\SupportTickets\Pages\ManageSupportTickets;
use App\Livewire\SupportCenter;
use App\Livewire\SupportContact;
use App\Livewire\SupportTickets;
use App\Models\SupportArticle;
use App\Models\SupportCategory;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketUpdate;
use Database\Seeders\SupportCategorySeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

test('support homepage and published articles are accessible while drafts and future articles are hidden', function () {
    $article = SupportArticle::factory()->create(['title' => 'Public help guide']);
    $draft = SupportArticle::factory()->create(['title' => 'Private draft guide', 'status' => 'draft']);
    $future = SupportArticle::factory()->create(['title' => 'Future guide', 'published_at' => now()->addDay()]);
    $this->get(route('support'))->assertOk()->assertSee('Public help guide')->assertDontSee('Private draft guide')->assertDontSee('Future guide');
    $this->get(route('support.article', $article->slug))->assertOk()->assertSee($article->content)->assertSee($article->category->name);
    $this->get(route('support.article', $draft->slug))->assertNotFound();
    $this->get(route('support.article', $future->slug))->assertNotFound();
});
test('support search includes article content and combines category filters', function () {
    $matching = SupportArticle::factory()->create(['title' => 'Creator guidance', 'content' => 'Learn about public portfolios and original work.']);
    SupportArticle::factory()->create(['title' => 'Unrelated account guide']);
    Livewire::test(SupportCenter::class)->set('search', 'portfolios')->assertSee('Creator guidance')->assertDontSee('Unrelated account guide')
        ->set('category', $matching->category->slug)->assertSee('Creator guidance')->set('category', 'unknown')->assertSee('No answers found just yet.')->call('clearFilters')->assertSet('search', '')->assertSet('category', '');
});
test('guest can submit a persisted support request and receives a unique reference without false mail claims', function () {
    Notification::fake();
    $this->get(route('support.contact'))->assertOk();
    Livewire::test(SupportContact::class)->set('name', 'Guest Person')->set('email', 'guest@example.com')->set('userType', 'other')
        ->set('category', 'technical')->set('subject', 'Cannot open a creator profile')->set('description', 'The creator profile does not load when I follow the directory link.')
        ->set('relatedReference', 'ENQUIRY-EXAMPLE')->call('submit')->assertHasNoErrors()->assertSet('emailQueued', false)->assertSee('We have your request.')
        ->assertSee('No email notification was queued.')->call('submit');
    $this->assertDatabaseCount('support_tickets', 1);
    $ticket = SupportTicket::first();
    expect($ticket->reference)->toStartWith('COL-')->toHaveLength(30);
    expect($ticket->user_id)->toBeNull();
    $this->assertDatabaseHas('support_tickets', ['email' => 'guest@example.com', 'status' => 'open', 'related_reference' => 'ENQUIRY-EXAMPLE']);
    Notification::assertNothingSent();
});
test('authenticated submission associates the signed in account and uses its trusted email', function () {
    $user = User::factory()->create(['role' => 'brand']);
    Livewire::actingAs($user)->test(SupportContact::class)->set('email', 'someone-else@example.com')->set('subject', 'Campaign moderation question')
        ->set('description', 'Please help me understand the moderation of my campaign submission.')->call('submit')->assertHasNoErrors();
    $this->assertDatabaseHas('support_tickets', ['user_id' => $user->id, 'email' => $user->email]);
    $this->actingAs($user)->get(route('support.tickets'))->assertOk()->assertSee('Campaign moderation question');
});
test('ticket submission rejects invalid fields and honeypot spam', function () {
    Livewire::test(SupportContact::class)->set('email', 'invalid')->set('userType', 'admin')->set('category', 'arbitrary')
        ->set('website', 'https://spam.example')->call('submit')->assertHasErrors(['name', 'email', 'userType', 'category', 'subject', 'description', 'website']);
    $this->assertDatabaseCount('support_tickets', 0);
});
test('ticket submission enforces its rate limit inside Livewire actions', function () {
    RateLimiter::hit('support-submit:'.hash('sha256', '127.0.0.1'), 600);
    for ($attempt = 0; $attempt < 4; $attempt++) {
        RateLimiter::hit('support-submit:'.hash('sha256', '127.0.0.1'), 600);
    }
    Livewire::test(SupportContact::class)->call('submit')->assertHasErrors('subject')->assertSee('Too many requests.');
    $this->assertDatabaseCount('support_tickets', 0);
});
test('ticket ownership and private staff notes are enforced on every request', function () {
    $ticket = SupportTicket::factory()->create(['subject' => 'Private ownership issue']);
    SupportMessage::factory()->create(['support_ticket_id' => $ticket->id, 'staff' => true, 'body' => 'Customer visible staff response']);
    SupportMessage::factory()->create(['support_ticket_id' => $ticket->id, 'staff' => true, 'internal' => true, 'body' => 'Sensitive internal-only note']);
    $other = User::factory()->create();
    $this->actingAs($other)->get(route('support.tickets'))->assertOk()->assertDontSee('Private ownership issue');
    $this->actingAs($other)->get(route('support.ticket', $ticket->reference))->assertNotFound();
    Livewire::actingAs($other)->test(SupportTickets::class, ['reference' => $ticket->reference])->assertNotFound();
    $this->actingAs($ticket->user)->get(route('support.ticket', $ticket->reference))->assertOk()->assertSee('Customer visible staff response')->assertDontSee('Sensitive internal-only note');
});
test('guest history redirects to login and an account cannot claim guest tickets by matching email', function () {
    $user = User::factory()->create();
    $ticket = SupportTicket::factory()->create(['user_id' => null, 'email' => $user->email]);
    $this->get(route('support.tickets'))->assertRedirect(route('login'));
    $this->actingAs($user)->get(route('support.ticket', $ticket->reference))->assertNotFound();
});
test('owners can follow up on open tickets while resolved and closed tickets reject replies', function () {
    $ticket = SupportTicket::factory()->create(['status' => 'waiting_for_customer']);
    Livewire::actingAs($ticket->user)->test(SupportTickets::class, ['reference' => $ticket->reference])->set('body', 'Here are the additional details you requested.')->call('reply')->assertHasNoErrors();
    $this->assertDatabaseHas('support_messages', ['support_ticket_id' => $ticket->id, 'user_id' => $ticket->user_id, 'staff' => false, 'internal' => false]);
    $this->assertDatabaseHas('support_tickets', ['id' => $ticket->id, 'status' => 'open']);
    $ticket->forceFill(['status' => 'closed'])->save();
    Livewire::actingAs($ticket->user)->test(SupportTickets::class, ['reference' => $ticket->reference])->set('body', 'Another follow-up message.')->call('reply')->assertForbidden();
    $this->assertDatabaseCount('support_messages', 1);
});
test('support policy allows owners to view and reply but reserves status and notes for verified staff', function () {
    $ticket = SupportTicket::factory()->create();
    $other = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    expect(Gate::forUser($ticket->user)->allows('view', $ticket))->toBeTrue();
    expect(Gate::forUser($ticket->user)->allows('update', $ticket))->toBeFalse();
    expect(Gate::forUser($other)->allows('view', $ticket))->toBeFalse();
    expect(Gate::forUser($admin)->allows('update', $ticket))->toBeTrue();
    $admin->forceFill(['email_verified_at' => null])->save();
    expect(Gate::forUser($admin)->allows('update', $ticket))->toBeFalse();
});
test('admin changes ticket status replies and writes private notes through Filament', function () {
    Notification::fake();
    $admin = User::factory()->create(['role' => 'admin']);
    $ticket = SupportTicket::factory()->create();
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Livewire::actingAs($admin)->test(ManageSupportTickets::class)
        ->callAction(TestAction::make('status')->table($ticket), ['status' => 'in_progress'])->assertHasNoActionErrors()
        ->callAction(TestAction::make('reply')->table($ticket), ['body' => 'We have reviewed your request and need more details.'])->assertHasNoActionErrors()
        ->callAction(TestAction::make('note')->table($ticket), ['body' => 'Private staff investigation details.'])->assertHasNoActionErrors()
        ->mountAction(TestAction::make('view')->table($ticket))->assertMountedActionModalSee('Private staff investigation details.')->assertMountedActionModalSee('INTERNAL NOTE');
    $this->assertDatabaseHas('support_tickets', ['id' => $ticket->id, 'status' => 'waiting_for_customer']);
    $this->assertDatabaseHas('support_messages', ['support_ticket_id' => $ticket->id, 'internal' => true, 'staff' => true, 'body' => 'Private staff investigation details.']);
    $this->assertDatabaseHas('support_messages', ['support_ticket_id' => $ticket->id, 'internal' => false, 'staff' => true]);
    expect($ticket->fresh()->status_changed_at)->not->toBeNull();
    Notification::assertNothingSent();
});
test('support administration is denied to non staff roles', function (string $role) {
    $this->actingAs(User::factory()->create(['role' => $role]))->get('/admin/support-tickets')->assertForbidden();
    $this->get('/admin/support-articles')->assertForbidden();
    $this->get('/admin/support-categories')->assertForbidden();
})->with(['creator', 'brand', 'customer']);
test('admin can publish articles through Filament and unpublishing hides them', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = SupportCategory::factory()->create();
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Livewire::actingAs($admin)->test(ManageSupportArticles::class)->callAction('create', [
        'support_category_id' => $category->id, 'title' => 'An original help guide', 'slug' => 'original-help-guide', 'summary' => 'A useful guide.', 'content' => 'A clear explanation of the current application functionality.', 'status' => 'published', 'published_at' => now()->subMinute()->toDateTimeString(), 'sort_order' => 0,
    ])->assertHasNoActionErrors();
    $this->get(route('support.article', 'original-help-guide'))->assertOk()->assertSee('An original help guide');
    $article = SupportArticle::first();
    Livewire::test(ManageSupportArticles::class)->callAction(TestAction::make('edit')->table($article), ['status' => 'draft'])->assertHasNoActionErrors();
    $this->get(route('support.article', 'original-help-guide'))->assertNotFound();
});
test('configured email queues support notifications and internal notes send none', function () {
    Notification::fake();
    config(['mail.default' => 'smtp']);
    $ticket = SupportTicket::factory()->create();
    $workflow = app(ManageSupportTicket::class);
    expect($workflow->notify($ticket, 'Request received.'))->toBeTrue();
    Notification::assertSentOnDemand(SupportTicketUpdate::class, fn ($notification, $channels, $notifiable) => $notification->reference === $ticket->reference && $notifiable->routes['mail'] === $ticket->email);
});
test('support content and private replies escape HTML', function () {
    $article = SupportArticle::factory()->create(['content' => '<script>alert(1)</script>']);
    $this->get(route('support.article', $article->slug))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    $ticket = SupportTicket::factory()->create(['description' => '<script>private()</script>']);
    $this->actingAs($ticket->user)->get(route('support.ticket', $ticket->reference))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>private()</script>', false);
    $mail = (new SupportTicketUpdate($ticket->reference, '<script>mail()</script>', true))->toMail($ticket->user)->render()->toHtml();
    expect($mail)->toContain('&lt;script&gt;')->not->toContain('<script>mail()</script>');
});
test('help content seeding is repeatable and preserves staff edits', function () {
    $this->seed(SupportCategorySeeder::class);
    $article = SupportArticle::first();
    $article->update(['content' => 'Staff edited guidance']);
    $this->seed(SupportCategorySeeder::class);
    $this->assertDatabaseCount('support_categories', 6);
    $this->assertDatabaseCount('support_articles', 22);
    $this->assertDatabaseHas('support_articles', ['id' => $article->id, 'content' => 'Staff edited guidance']);
});

test('internal notes never send mail even with email configured', function () {
    Notification::fake();
    config(['mail.default' => 'smtp']);
    $ticket = SupportTicket::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    app(ManageSupportTicket::class)->respond($admin, $ticket, 'Private investigation note for staff only.', true);

    $this->assertDatabaseHas('support_messages', ['support_ticket_id' => $ticket->id, 'internal' => true]);
    Notification::assertNothingSent();
});

test('ticket remains saved when email dispatch fails', function () {
    $component = Livewire::test(SupportContact::class)->set('name', 'Guest Example')->set('email', 'guest@example.com')->set('subject', 'Help with my creator profile')
        ->set('description', 'Please help me understand why my approved profile is missing.');
    config(['mail.default' => 'smtp']);
    Notification::shouldReceive('send')->once()->andThrow(new RuntimeException('Mail transport unavailable'));

    $component->call('submit')->assertHasNoErrors()->assertSet('emailQueued', false)->assertSee('We have your request.');

    $this->assertDatabaseCount('support_tickets', 1);
});

test('support rejects malformed query parameters before hydration', function () {
    $this->get(route('support', ['search' => ['invalid']]))->assertBadRequest();
});

test('all published support guides remain accessible on one page including old paginated URLs', function () {
    $this->seed(SupportCategorySeeder::class);
    $articles = SupportArticle::published()->get();

    $response = $this->get(route('support', ['page' => 3]))->assertOk()->assertSee('22 guides');

    foreach ($articles as $article) {
        $response->assertSee($article->title)->assertSee('href="'.route('support.article', $article->slug).'"', false);
    }
    $response->assertDontSee('Showing 19 to 22')->assertDontSee('aria-label="Pagination Navigation"', false);
});

test('support search matches title summary content and category text without case sensitivity', function (string $search) {
    $category = SupportCategory::factory()->create(['name' => 'Creator Help', 'description' => 'Advice for onboarding and account setup.']);
    SupportArticle::factory()->create(['support_category_id' => $category->id, 'title' => 'Portfolio Publishing', 'summary' => 'Showcase your collection.', 'content' => 'Original photographs help brands discover your work.']);
    SupportArticle::factory()->create(['title' => 'Unrelated billing guide', 'summary' => 'Billing terms', 'content' => 'Payment terms']);
    SupportArticle::factory()->create(['support_category_id' => $category->id, 'title' => 'Hidden category draft', 'status' => 'draft']);

    Livewire::test(SupportCenter::class)->set('search', $search)->assertSee('Portfolio Publishing')->assertDontSee('Unrelated billing guide')->assertDontSee('Hidden category draft');
})->with(['title' => '  PORTFOLIO  ', 'summary' => 'COLLECTION', 'content' => 'PHOTOGRAPHS', 'category name' => 'CREATOR HELP', 'category description' => 'ONBOARDING']);

test('clearing support search keeps the chosen category and showing all guides clears both filters', function () {
    $article = SupportArticle::factory()->create(['title' => 'Chosen category guide']);
    SupportArticle::factory()->create(['title' => 'Another category guide']);

    Livewire::test(SupportCenter::class)->set('category', $article->category->slug)->set('search', 'no-match-keyword')->assertSee('No answers found just yet.')
        ->call('clearSearch')->assertSet('search', '')->assertSet('category', $article->category->slug)->assertSee('Chosen category guide')->assertDontSee('Another category guide')
        ->call('clearFilters')->assertSet('category', '')->assertSee('Another category guide');
});
