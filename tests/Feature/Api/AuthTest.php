<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/* REGISTER */

test('user can register with valid data', function () {
    /* ARRANGE */
    $payload = [
        'name'                  => 'Alice',
        'email'                 => 'alice@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ];

    /* ACT */
    $response = $this->postJson('/api/v1/register', $payload);

    /* ASSERT */
    $response->assertStatus(201)
             ->assertJsonPath('status', 'success')
             ->assertJsonStructure(['data' => ['user', 'token']]);
});

test('register returns 422 if email is missing', function () {
    /* ARRANGE */
    $payload = [
        'name'                  => 'Alice',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ];

    /* ACT */
    $response = $this->postJson('/api/v1/register', $payload);

    /* ASSERT */
    $response->assertStatus(422)
             ->assertJsonPath('status', 'error');
});

/* LOGIN */

test('user can login with valid credentials', function () {
    /* ARRANGE */
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    /* ACT */
    $response = $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'secret123',
    ]);

    /* ASSERT */
    $response->assertStatus(200)
             ->assertJsonPath('status', 'success')
             ->assertJsonStructure(['data' => ['user', 'token']]);
});

test('login returns 401 with wrong password', function () {
    /* ARRANGE */
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    /* ACT */
    $response = $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    /* ASSERT */
    $response->assertStatus(401)
             ->assertJsonPath('status', 'error');
});

/* LOGOUT */

test('authenticated user can logout', function () {
    /* ARRANGE */
    $user  = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    /* ACT */
    $response = $this->withToken($token)
                     ->postJson('/api/v1/logout');

    /* ASSERT */
    $response->assertStatus(200)
             ->assertJsonPath('status', 'success');
});

test('unauthenticated user cannot logout', function () {
    /* ACT */
    $response = $this->postJson('/api/v1/logout');

    /* ASSERT */
    $response->assertStatus(401);
});
