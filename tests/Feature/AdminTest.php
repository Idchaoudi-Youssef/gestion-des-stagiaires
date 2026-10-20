<?php

use App\Enums\InternshipStatus;
use App\Enums\ReportState;
use App\Enums\UserType;
use App\Models\Department;
use App\Models\Internship;
use App\Models\Report;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/

test('an administrator can create a user', function () {
    $admin = User::factory()->admin()->create();
    $department = Department::factory()->create();

    $response = $this->actingAs($admin)->post('/admin/users', [
        'usertype' => UserType::Intern->value,
        'name' => 'New Intern',
        'phone' => '06 00 00 00 00',
        'address' => '1 test street',
        'email' => 'new.intern@example.com',
        'password' => 'password',
        'department_id' => $department->id,
    ]);

    $response->assertRedirect(route('admin.users'));

    $user = User::where('email', 'new.intern@example.com')->firstOrFail();

    expect($user->usertype)->toBe(UserType::Intern);

    // An intern always has an internship record.
    expect($user->internship)->not->toBeNull();
    expect($user->internship->status)->toBe(InternshipStatus::NotStarted);
});

test('a department is required for an intern', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin/users', [
        'usertype' => UserType::Intern->value,
        'name' => 'New Intern',
        'phone' => '06 00 00 00 00',
        'address' => '1 test street',
        'email' => 'new.intern@example.com',
        'password' => 'password',
        'department_id' => null,
    ])->assertSessionHasErrors('department_id');
});

test('an administrator can not delete another administrator', function () {
    $admin = User::factory()->admin()->create();
    $otherAdmin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete('/admin/users/'.$otherAdmin->id)
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
});

test('an administrator can not delete their own account from the user list', function () {
    $admin = User::factory()->admin()->create();
    $intern = User::factory()->intern()->create();

    $this->actingAs($admin)
        ->delete('/admin/users/'.$admin->id)
        ->assertRedirect(route('admin.users'));

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('an administrator can delete an intern', function () {
    $admin = User::factory()->admin()->create();
    $intern = User::factory()->intern()->create();

    $this->actingAs($admin)
        ->delete('/admin/users/'.$intern->id)
        ->assertRedirect(route('admin.users'));

    $this->assertDatabaseMissing('users', ['id' => $intern->id]);
});

test('updating an intern does not create a second internship', function () {
    $admin = User::factory()->admin()->create();
    $department = Department::factory()->create();
    $intern = User::factory()->intern()->create(['department_id' => $department->id]);

    Internship::create([
        'intern_id' => $intern->id,
        'status' => InternshipStatus::NotStarted->value,
    ]);

    $this->actingAs($admin)->put('/admin/users/'.$intern->id, [
        'usertype' => UserType::Intern->value,
        'name' => 'Renamed Intern',
        'phone' => '06 00 00 00 00',
        'address' => '1 test street',
        'email' => $intern->email,
        'password' => '',
        'department_id' => $department->id,
    ])->assertRedirect(route('admin.users'));

    expect($intern->refresh()->name)->toBe('Renamed Intern');
    expect(Internship::where('intern_id', $intern->id)->count())->toBe(1);
});

test('deleting a department keeps its users', function () {
    $admin = User::factory()->admin()->create();
    $department = Department::factory()->create();
    $intern = User::factory()->intern()->create(['department_id' => $department->id]);

    $this->actingAs($admin)
        ->delete('/admin/departments/'.$department->id)
        ->assertRedirect(route('departments.index'));

    $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    $this->assertDatabaseHas('users', ['id' => $intern->id]);
    expect($intern->refresh()->department_id)->toBeNull();
});

test('an administrator can only assign interns to supervisors', function () {
    $admin = User::factory()->admin()->create();
    $intern = User::factory()->intern()->create();
    $supervisor = User::factory()->supervisor()->create();

    $this->actingAs($admin)->post('/admin/assign', [
        'intern_id' => $intern->id,
        'supervisor_ids' => [$supervisor->id],
    ])->assertRedirect(route('admin.showAssignForm'));

    expect($intern->supervisors()->pluck('users.id')->all())->toBe([$supervisor->id]);
});

test('an administrator can not assign a non supervisor', function () {
    $admin = User::factory()->admin()->create();
    $intern = User::factory()->intern()->create();
    $anotherIntern = User::factory()->intern()->create();

    $this->actingAs($admin)->post('/admin/assign', [
        'intern_id' => $intern->id,
        'supervisor_ids' => [$anotherIntern->id],
    ])->assertSessionHasErrors('supervisor_ids');

    expect($intern->supervisors()->count())->toBe(0);
});

test('an administrator can approve a final report', function () {
    $admin = User::factory()->admin()->create();
    $report = Report::factory()->final()->create();

    $this->actingAs($admin)
        ->post('/admin/approve-report/'.$report->id)
        ->assertRedirect(route('admin.finalReports'));

    expect($report->refresh()->state)->toBe(ReportState::Approved);
    expect($report->refresh()->admin_id)->toBe($admin->id);
});

test('an administrator can reject a final report and keep the reason', function () {
    $admin = User::factory()->admin()->create();
    $report = Report::factory()->final()->create();

    $this->actingAs($admin)
        ->post('/admin/reject-report/'.$report->id, ['reject_reason' => 'The report is incomplete.'])
        ->assertRedirect(route('admin.finalReports'));

    expect($report->refresh()->state)->toBe(ReportState::Rejected);
    expect($report->refresh()->reject_reason)->toBe('The report is incomplete.');
});

test('the administrator dashboard is reachable and shows the counters', function () {
    $admin = User::factory()->admin()->create();
    $intern = User::factory()->intern()->create();

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertOk();
    $response->assertViewHas('internCount', 1);
    $response->assertViewHas('supervisorCount', 0);
});
