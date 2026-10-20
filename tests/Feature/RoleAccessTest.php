<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Role based access
|--------------------------------------------------------------------------
|
| Every page of the platform belongs to one role. A user must never reach
| the pages of another role, and an account that is still pending must not
| reach any dashboard.
|
*/

test('a guest is sent to the login page', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->get('/supervisor/dashboard')->assertRedirect('/login');
    $this->get('/intern/dashboard')->assertRedirect('/login');
});

test('an administrator can reach the administrator pages', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    $this->actingAs($admin)->get('/admin/users')->assertOk();
    $this->actingAs($admin)->get('/admin/departments')->assertOk();
});

test('a supervisor can not reach the administrator pages', function () {
    $supervisor = User::factory()->supervisor()->create();

    $this->actingAs($supervisor)->get('/admin/dashboard')->assertForbidden();
    $this->actingAs($supervisor)->get('/admin/users')->assertForbidden();
});

test('an intern can not reach the administrator pages', function () {
    $intern = User::factory()->intern()->create();

    $this->actingAs($intern)->get('/admin/dashboard')->assertForbidden();
});

test('a supervisor can not reach the intern pages', function () {
    $supervisor = User::factory()->supervisor()->create();

    $this->actingAs($supervisor)->get('/intern/dashboard')->assertForbidden();
});

test('an intern can not reach the supervisor pages', function () {
    $intern = User::factory()->intern()->create();

    $this->actingAs($intern)->get('/supervisor/dashboard')->assertForbidden();
});

test('a pending account only reaches the pending page', function () {
    $pending = User::factory()->pending()->create();

    $this->actingAs($pending)->get('/pending')->assertOk();
    $this->actingAs($pending)->get('/intern/dashboard')->assertForbidden();
    $this->actingAs($pending)->get('/supervisor/dashboard')->assertForbidden();
    $this->actingAs($pending)->get('/admin/dashboard')->assertForbidden();
});

test('the dashboard sends the user to the page of their role', function () {
    $admin = User::factory()->admin()->create();
    $supervisor = User::factory()->supervisor()->create();
    $intern = User::factory()->intern()->create();
    $pending = User::factory()->pending()->create();

    $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    $this->actingAs($supervisor)->get('/dashboard')->assertRedirect(route('supervisor.dashboard'));
    $this->actingAs($intern)->get('/dashboard')->assertRedirect(route('intern.dashboard'));
    $this->actingAs($pending)->get('/dashboard')->assertRedirect(route('pending'));
});

test('the role is read from the database so it can be revoked immediately', function () {
    $intern = User::factory()->intern()->create();

    $this->actingAs($intern)->get('/intern/dashboard')->assertOk();

    // The administrator demotes the intern.
    $intern->update(['usertype' => 'pending']);

    $this->actingAs($intern)->get('/intern/dashboard')->assertForbidden();
});
