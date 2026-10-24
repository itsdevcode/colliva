<?php

use App\Models\User;

test('pricing explains free access separately from creator collaboration prices', function () {
    $this->get(route('pricing'))->assertOk()
        ->assertSee('Pricing & Fees')
        ->assertSee('For Creators')
        ->assertSee('For Brands & Customers')
        ->assertSee('Creator registration')
        ->assertSee('Creating and maintaining your profile')
        ->assertSee('Building and showcasing your portfolio')
        ->assertSee('Receiving and responding to enquiries')
        ->assertSee('Brand and Customer registration')
        ->assertSee('Discovering creators')
        ->assertSee('Viewing creator profiles and portfolios')
        ->assertSee('Sending initial enquiries')
        ->assertSee('Creator collaboration prices are independent of platform access.')
        ->assertSee('Colliva does not collect payments or provide escrow.')
        ->assertSee('Could platform fees change in the future?');
});

test('pricing footer navigation leads to the public pricing page', function () {
    $this->get(route('home'))->assertOk()->assertSeeInOrder([
        '<footer class="site-footer">',
        'href="'.route('pricing').'"',
        'Pricing &amp; Fees',
        '</footer>',
    ], false);

    $this->get('/pricing')->assertOk()->assertSee('Free platform access does not mean free creator work.');
});

test('pricing links preserve each dedicated signup destination', function (string $role) {
    $this->get(route('pricing'))->assertOk()->assertSee('href="'.route('signup', $role).'"', false);

    $this->get(route('signup', $role))->assertOk()->assertViewHas('role', $role);
})->with(['creator', 'brand', 'customer']);

test('existing members can read pricing without changing their account type', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $this->actingAs($user)->get(route('pricing'))->assertOk()->assertSee('For Creators')->assertSee('For Brands & Customers');

    expect($user->fresh()->role)->toBe('customer');
});
