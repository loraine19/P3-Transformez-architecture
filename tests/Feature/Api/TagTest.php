<?php

use App\Models\Tag;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/* INDEX */

test('unauthenticated user cannot list tags', function () {
    /* ACT */
    $response = $this->getJson('/api/v1/tags');

    /* ASSERT */
    $response->assertStatus(401);
});

test('authenticated user can list their tags', function () {
    /* ARRANGE */
    $user = User::factory()->create();
    Tag::factory()->count(3)->create(['user_id' => $user->id]);

    /* ACT */
    $response = $this->actingAs($user)
                     ->getJson('/api/v1/tags');

    /* ASSERT */
    $response->assertStatus(200)
             ->assertJsonPath('status', 'success')
             ->assertJsonCount(3, 'data');
});

/* STORE */

test('authenticated user can create a tag', function () {
    /* ARRANGE */
    $user = User::factory()->create();

    /* ACT */
    $response = $this->actingAs($user)
                     ->postJson('/api/v1/tags', ['name' => 'travail']);

    /* ASSERT */
    $response->assertStatus(201)
             ->assertJsonPath('status', 'success')
             ->assertJsonPath('data.name', 'travail');
});

test('create tag returns 422 if name is missing', function () {
    /* ARRANGE */
    $user = User::factory()->create();

    /* ACT */
    $response = $this->actingAs($user)
                     ->postJson('/api/v1/tags', []);

    /* ASSERT */
    $response->assertStatus(422)
             ->assertJsonPath('status', 'error');
});

test('unauthenticated user cannot create a tag', function () {
    /* ACT */
    $response = $this->postJson('/api/v1/tags', ['name' => 'travail']);

    /* ASSERT */
    $response->assertStatus(401);
});
