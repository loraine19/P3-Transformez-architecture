<?php

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/* INDEX */

test('unauthenticated user cannot list notes', function () {
    /* ACT */
    $response = $this->getJson('/api/v1/notes');

    /* ASSERT */
    $response->assertStatus(401);
});

test('authenticated user can list their notes', function () {
    /* ARRANGE */
    $user = User::factory()->create();
    Note::factory()->count(3)->create(['user_id' => $user->id]);

    /* ACT */
    $response = $this->actingAs($user)
                     ->getJson('/api/v1/notes');

    /* ASSERT */
    $response->assertStatus(200)
             ->assertJsonPath('status', 'success')
             ->assertJsonCount(3, 'data');
});

test('user only sees their own notes', function () {
    /* ARRANGE */
    $user  = User::factory()->create();
    $other = User::factory()->create();
    Note::factory()->count(2)->create(['user_id' => $user->id]);
    Note::factory()->count(5)->create(['user_id' => $other->id]);

    /* ACT */
    $response = $this->actingAs($user)
                     ->getJson('/api/v1/notes');

    /* ASSERT */
    $response->assertStatus(200)
             ->assertJsonCount(2, 'data');
});

/* STORE */

test('authenticated user can create a note', function () {
    /* ARRANGE */
    $user = User::factory()->create();
    $tag  = Tag::factory()->create(['user_id' => $user->id]);

    /* ACT */
    $response = $this->actingAs($user)
                     ->postJson('/api/v1/notes', [
                         'text'   => 'Ma première note',
                         'tag_id' => $tag->id,
                     ]);

    /* ASSERT */
    $response->assertStatus(201)
             ->assertJsonPath('status', 'success')
             ->assertJsonPath('data.text', 'Ma première note');
});

test('create note returns 422 if text is missing', function () {
    /* ARRANGE */
    $user = User::factory()->create();
    $tag  = Tag::factory()->create(['user_id' => $user->id]);

    /* ACT */
    $response = $this->actingAs($user)
                     ->postJson('/api/v1/notes', ['tag_id' => $tag->id]);

    /* ASSERT */
    $response->assertStatus(422)
             ->assertJsonPath('status', 'error');
});

test('unauthenticated user cannot create a note', function () {
    /* ACT */
    $response = $this->postJson('/api/v1/notes', [
        'text'   => 'Note',
        'tag_id' => 1,
    ]);

    /* ASSERT */
    $response->assertStatus(401);
});

/* DESTROY */

test('authenticated user can delete their own note', function () {
    /* ARRANGE */
    $user = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $user->id]);

    /* ACT */
    $response = $this->actingAs($user)
                     ->deleteJson("/api/v1/notes/{$note->id}");

    /* ASSERT */
    $response->assertStatus(200)
             ->assertJsonPath('status', 'success');
});

test('user cannot delete a note belonging to another user', function () {
    /* ARRANGE */
    $user  = User::factory()->create();
    $other = User::factory()->create();
    $note  = Note::factory()->create(['user_id' => $other->id]);
    $token = $user->createToken('test')->plainTextToken;

    /* ACT */
    $response = $this->withToken($token)
                     ->deleteJson("/api/v1/notes/{$note->id}");

    /* ASSERT */
    $response->assertStatus(403)
             ->assertJsonPath('status', 'error');
});

test('delete returns 404 if note does not exist', function () {
    /* ARRANGE */
    $user = User::factory()->create();

    /* ACT */
    $response = $this->actingAs($user)
                     ->deleteJson('/api/v1/notes/99999');

    /* ASSERT */
    $response->assertStatus(404)
             ->assertJsonPath('status', 'error');
});

test('unauthenticated user cannot delete a note', function () {
    /* ARRANGE */
    $note = Note::factory()->create();

    /* ACT */
    $response = $this->deleteJson("/api/v1/notes/{$note->id}");

    /* ASSERT */
    $response->assertStatus(401);
});
