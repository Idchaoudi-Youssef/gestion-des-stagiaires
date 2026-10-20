<?php

use App\Models\Department;
use App\Models\Internship;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Smoke test
|--------------------------------------------------------------------------
|
| Every page of the platform is opened once per role. The test only fails
| when a page answers with a server error, which catches the broken views
| and the missing assets.
|
*/

function openEveryPage(User $user, array $routes): void
{
    foreach ($routes as $route) {
        $response = test()->actingAs($user)->get($route);

        expect($response->status(), "GET {$route} answered with {$response->status()}")
            ->not->toBe(500);
    }
}

test('every page of the administrator opens without a server error', function () {
    $admin = User::factory()->admin()->create();

    openEveryPage($admin, [
        '/admin/dashboard',
        '/admin/users',
        '/admin/users/create',
        '/admin/internslist',
        '/admin/departments',
        '/admin/departments/create',
        '/admin/assign',
        '/admin/final-reports',
        '/profile',
        '/pending',
    ]);
});

test('every page of the supervisor opens without a server error', function () {
    $supervisor = User::factory()->supervisor()->create();

    openEveryPage($supervisor, [
        '/supervisor/dashboard',
        '/supervisor/tasks',
        '/supervisor/tasks/create',
        '/supervisor/weekly-reports',
        '/profile',
    ]);
});

test('every page of the intern opens without a server error', function () {
    $intern = User::factory()->intern()->create();

    openEveryPage($intern, [
        '/intern/dashboard',
        '/intern/tasks',
        '/intern/reports',
        '/intern/reports/create',
        '/intern/attestation',
        '/profile',
    ]);
});

test('every public page opens without a server error', function () {
    foreach (['/', '/login', '/register', '/forgot-password'] as $route) {
        $response = test()->get($route);

        expect($response->status(), "GET {$route} answered with {$response->status()}")
            ->not->toBe(500);
    }
});

test('the edit pages open with real data', function () {
    $admin = User::factory()->admin()->create();
    $department = Department::factory()->create();
    $intern = User::factory()->intern()->create(['department_id' => $department->id]);
    $supervisor = User::factory()->supervisor()->create();

    Internship::create(['intern_id' => $intern->id, 'status' => 'not_started']);
    Task::factory()->create(['intern_id' => $intern->id, 'supervisor_id' => $supervisor->id]);
    Report::factory()->final()->create(['intern_id' => $intern->id]);

    $routes = [
        "/admin/users/{$intern->id}/edit",
        "/admin/{$intern->id}/add-internship-data",
        "/admin/{$intern->id}/edit-internship-data",
        '/supervisor/tasks/'.Task::first()->id.'/edit',
        "/admin/departments/{$department->id}/edit",
    ];

    foreach ($routes as $route) {
        $response = test()->actingAs($admin)->get($route);

        expect($response->status(), "GET {$route} answered with {$response->status()}")
            ->not->toBe(500);
    }
});
