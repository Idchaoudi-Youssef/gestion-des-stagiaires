<?php

namespace Database\Seeders;

use App\Enums\InternshipStatus;
use App\Enums\TaskState;
use App\Enums\UserType;
use App\Models\Department;
use App\Models\Internship;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo data so the application can be tried out right after the install.
 *
 * Every account uses the password "password".
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::create(['name' => 'Information Technology']);
        $hr = Department::create(['name' => 'Human Resources']);
        Department::create(['name' => 'Finance']);

        $admin = User::create([
            'name' => 'Demo Admin',
            'email' => 'demo.admin@example.com',
            'password' => 'password',
            'usertype' => UserType::Admin->value,
            'phone' => '06 00 00 00 00',
            'address' => 'Demo address',
            'email_verified_at' => now(),
            'department_id' => $it->id,
        ]);

        $supervisor = User::create([
            'name' => 'Demo Supervisor',
            'email' => 'demo.supervisor@example.com',
            'password' => 'password',
            'usertype' => UserType::Supervisor->value,
            'phone' => '06 00 00 00 00',
            'address' => 'Demo address',
            'email_verified_at' => now(),
            'department_id' => $it->id,
        ]);

        $intern = User::create([
            'name' => 'Demo Intern',
            'email' => 'demo.intern@example.com',
            'password' => 'password',
            'usertype' => UserType::Intern->value,
            'phone' => '06 00 00 00 00',
            'address' => 'Demo address',
            'email_verified_at' => now(),
            'department_id' => $it->id,
        ]);

        // An account that registered on their own and is still waiting for
        // an administrator to give it a role.
        User::create([
            'name' => 'Demo Pending',
            'email' => 'demo.pending@example.com',
            'password' => 'password',
            'usertype' => UserType::Pending->value,
            'phone' => '06 00 00 00 00',
            'address' => 'Demo address',
        ]);

        $supervisor->interns()->attach($intern->id);

        Internship::create([
            'intern_id' => $intern->id,
            'start_date' => now()->subMonths(2)->toDateString(),
            'end_date' => now()->addMonths(4)->toDateString(),
            'internship_period' => 6,
            'position' => 'Junior Web Developer',
            'status' => InternshipStatus::InPeriod->value,
        ]);

        Task::create([
            'title' => 'Set up the development environment',
            'description' => 'Install the stack and open the first pull request.',
            'intern_id' => $intern->id,
            'supervisor_id' => $supervisor->id,
            'state' => TaskState::Pending->value,
            'limit_date' => now()->addDays(3),
        ]);

        Task::create([
            'title' => 'Fix the login page',
            'description' => 'The session is not refreshed after the login.',
            'intern_id' => $intern->id,
            'supervisor_id' => $supervisor->id,
            'state' => TaskState::Completed->value,
            'limit_date' => now()->subDays(2),
        ]);

        Report::create([
            'content' => 'First weekly report of the internship.',
            'type' => 'weekly',
            'intern_id' => $intern->id,
            'supervisor_id' => $supervisor->id,
            'state' => 'pending',
        ]);

        $this->command?->info('Demo data created. Password of every account: "password"');
    }
}
