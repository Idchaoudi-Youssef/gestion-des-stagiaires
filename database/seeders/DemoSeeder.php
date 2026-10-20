<?php

namespace Database\Seeders;

use App\Enums\InternshipStatus;
use App\Enums\ReportState;
use App\Enums\ReportType;
use App\Enums\TaskState;
use App\Enums\UserType;
use App\Models\Department;
use App\Models\Internship;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Optional demo data, to explore the interface without creating anything.
 *
 * It is never run by `php artisan migrate --seed`, it has to be asked for:
 *
 *     php artisan db:seed --class=DemoSeeder
 *
 * The accounts are named "Demo ..." on purpose: nobody should ever mistake
 * them for a real person.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::firstOrCreate(['name' => 'Information Technology']);

        $admin = $this->user('Demo Admin', 'demo.admin@example.com', UserType::Admin, $department);
        $supervisor = $this->user('Demo Supervisor', 'demo.supervisor@example.com', UserType::Supervisor, $department);
        $intern = $this->user('Demo Intern', 'demo.intern@example.com', UserType::Intern, $department);

        $this->user('Demo Pending', 'demo.pending@example.com', UserType::Pending, $department);

        $supervisor->interns()->syncWithoutDetaching([$intern->id]);

        Internship::updateOrCreate(
            ['intern_id' => $intern->id],
            [
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => now()->addMonths(4)->toDateString(),
                'internship_period' => 6,
                'position' => 'Junior Web Developer',
                'status' => InternshipStatus::InPeriod->value,
            ]
        );

        Task::updateOrCreate(
            ['title' => 'Set up the development environment', 'intern_id' => $intern->id],
            [
                'description' => 'Install the stack and open the first pull request.',
                'supervisor_id' => $supervisor->id,
                'state' => TaskState::Pending->value,
                'limit_date' => now()->addDays(3),
            ]
        );

        Task::updateOrCreate(
            ['title' => 'Fix the login page', 'intern_id' => $intern->id],
            [
                'description' => 'The session is not refreshed after the login.',
                'supervisor_id' => $supervisor->id,
                'state' => TaskState::Completed->value,
                'limit_date' => now()->subDays(2),
            ]
        );

        Report::updateOrCreate(
            ['intern_id' => $intern->id, 'type' => ReportType::Weekly->value],
            [
                'content' => 'First weekly report of the internship.',
                'supervisor_id' => $supervisor->id,
                'state' => ReportState::Pending->value,
            ]
        );

        $this->command?->info('Demo data created. Every account uses the password "password".');
    }

    /**
     * Create or refresh a demo account.
     */
    protected function user(string $name, string $email, UserType $type, Department $department): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password',
                'usertype' => $type->value,
                'phone' => '06 00 00 00 00',
                'address' => 'Demo address',
                'email_verified_at' => now(),
                'department_id' => $department->id,
            ]
        );
    }
}
