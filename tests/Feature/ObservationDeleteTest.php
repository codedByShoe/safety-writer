<?php

use App\Models\Observation;
use App\Models\User;

test('guests cannot delete observations', function () {
    $user = User::factory()->create();
    $observation = Observation::factory()->for($user)->create();

    $response = $this
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->delete(route('observation.destroy', $observation));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('observations', ['id' => $observation->id]);
});

test('authenticated users can delete their own observations', function () {
    $user = User::factory()->create();
    $observation = Observation::factory()->for($user)->create();

    $response = $this
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($user)
        ->delete(route('observation.destroy', $observation));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Observation deleted successfully.');
    $this->assertDatabaseMissing('observations', ['id' => $observation->id]);
});

test('users cannot delete observations belonging to other users', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $observation = Observation::factory()->for($otherUser)->create();

    $response = $this
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($user)
        ->delete(route('observation.destroy', $observation));

    $response->assertForbidden();
    $this->assertDatabaseHas('observations', ['id' => $observation->id]);
});

test('deleting an observation removes it from the database', function () {
    $user = User::factory()->create();
    $observation = Observation::factory()->for($user)->create([
        'title' => 'Test Observation to Delete',
        'status' => 'draft',
    ]);

    $observationId = $observation->id;

    $this
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($user)
        ->delete(route('observation.destroy', $observation));

    expect(Observation::find($observationId))->toBeNull();
    $this->assertDatabaseMissing('observations', [
        'id' => $observationId,
        'title' => 'Test Observation to Delete',
    ]);
});

test('deleting finalized observations works correctly', function () {
    $user = User::factory()->create();
    $observation = Observation::factory()->for($user)->create([
        'status' => 'finalized',
    ]);

    $response = $this
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($user)
        ->delete(route('observation.destroy', $observation));

    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseMissing('observations', ['id' => $observation->id]);
});
