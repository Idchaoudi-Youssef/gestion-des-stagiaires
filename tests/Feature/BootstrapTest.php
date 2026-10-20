<?php

use App\Enums\UserType;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoSeeder;

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
|
| The installation ships without any account: the person who installs the
| application decides who the administrator is.
|
*/

test('the default installation creates no account', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::count())->toBe(0);
    expect(Department::count())->toBeGreaterThan(0);
});

test('the administrator can be created from the command line', function () {
    $this->artisan('app:create-admin', [
        '--name' => 'The Boss',
        '--email' => 'boss@example.com',
        '--password' => 'a-strong-password',
    ])->assertSuccessful();

    $admin = User::where('email', 'boss@example.com')->firstOrFail();

    expect($admin->name)->toBe('The Boss');
    expect($admin->usertype)->toBe(UserType::Admin);
    expect($admin->email_verified_at)->not->toBeNull();
    expect($admin->password)->not->toBe('a-strong-password');
});

test('the administrator can be attached to a department', function () {
    $this->artisan('app:create-admin', [
        '--name' => 'The Boss',
        '--email' => 'boss@example.com',
        '--password' => 'a-strong-password',
        '--department' => 'Direction',
    ])->assertSuccessful();

    expect(User::where('email', 'boss@example.com')->firstOrFail()->department->name)->toBe('Direction');
});

test('the command refuses an address that is already taken', function () {
    User::factory()->admin()->create(['email' => 'boss@example.com']);

    $this->artisan('app:create-admin', [
        '--name' => 'The Boss',
        '--email' => 'boss@example.com',
        '--password' => 'a-strong-password',
    ])->assertFailed();

    expect(User::where('email', 'boss@example.com')->count())->toBe(1);
});

test('the demo seeder is opt in and only creates obviously fake accounts', function () {
    $this->seed(DemoSeeder::class);

    expect(User::count())->toBe(4);

    foreach (User::all() as $user) {
        expect($user->name)->toStartWith('Demo ');
        expect($user->email)->toContain('demo.');
    }
});
