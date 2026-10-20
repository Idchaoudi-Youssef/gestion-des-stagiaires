<?php

use App\Enums\UserType;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '06 00 00 00 00',
        'address' => '1 test street',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('pending', absolute: false));

    $user = User::where('email', 'test@example.com')->firstOrFail();

    // A self registered account is not an intern until an administrator
    // gives it a role.
    expect($user->usertype)->toBe(UserType::Pending);
});

test('registration requires a phone number and an address', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['phone', 'address']);
    $this->assertGuest();
});

test('a registered account can not reach the intern dashboard', function () {
    $user = User::factory()->pending()->create();

    $this->actingAs($user)->get('/intern/dashboard')->assertForbidden();
});
