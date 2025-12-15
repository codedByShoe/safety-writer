<?php

use App\Models\User;

test('users with insufficient credits cannot create observations', function () {
    $user = User::factory()->create();

    // Set user credits to less than 100
    $user->creditAdd(50);

    $observationData = [
        'discipline' => 'Mechanical',
        'company' => 'Test Company',
        'location' => 'Building A',
        'observationType' => 'met',
        'intentionality' => 'intentional',
        'gap' => 'Test gap description',
        'whyDetails' => 'Test why details',
        'consequence' => null,
        'impactfulAction' => 'Test impactful action',
        'peerToPeer' => 'Test peer to peer',
    ];

    $response = $this
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($user)
        ->post(route('observation.store'), $observationData);

    $response->assertForbidden();
});

test('users with exactly 100 credits can create observations', function () {
    $user = User::factory()->create();

    // Set user credits to exactly 100
    $user->creditAdd(100);

    $observationData = [
        'discipline' => 'Mechanical',
        'company' => 'Test Company',
        'location' => 'Building A',
        'observationType' => 'met',
        'intentionality' => 'intentional',
        'gap' => 'Test gap description',
        'whyDetails' => 'Test why details',
        'consequence' => null,
        'impactfulAction' => 'Test impactful action',
        'peerToPeer' => 'Test peer to peer',
    ];

    $response = $this
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($user)
        ->post(route('observation.store'), $observationData);

    $response->assertRedirect();
    $this->assertDatabaseHas('observations', [
        'user_id' => $user->id,
    ]);
});

test('users with more than 100 credits can create observations', function () {
    $user = User::factory()->create();

    // Set user credits to more than 100
    $user->creditAdd(500);

    $observationData = [
        'discipline' => 'Electrical',
        'company' => 'Test Company',
        'location' => 'Building B',
        'observationType' => 'not-met',
        'intentionality' => 'convenience',
        'gap' => 'Test gap description',
        'whyDetails' => 'Test why details',
        'consequence' => 'Test consequence',
        'impactfulAction' => 'Test impactful action',
        'peerToPeer' => 'Test peer to peer',
    ];

    $response = $this
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($user)
        ->post(route('observation.store'), $observationData);

    $response->assertRedirect();
    $this->assertDatabaseHas('observations', [
        'user_id' => $user->id,
    ]);
});

test('observation index page shows insufficient credits alert when credits below 100', function () {
    $user = User::factory()->create();
    $user->creditAdd(50);

    $response = $this
        ->actingAs($user)
        ->get(route('observation'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('observations/Index')
        ->has('credits')
        ->where('credits', 50)
        ->where('hasInsufficientCredits', true)
    );
});

test('observation index page does not show alert when credits above 100', function () {
    $user = User::factory()->create();
    $user->creditAdd(200);

    $response = $this
        ->actingAs($user)
        ->get(route('observation'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('observations/Index')
        ->has('credits')
        ->where('credits', 200)
        ->where('hasInsufficientCredits', false)
    );
});
