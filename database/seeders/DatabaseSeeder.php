<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

/**
 * The default installation of the platform.
 *
 * It only creates the structure of the organisation: the departments.
 *
 * No account and no person is created here. The first administrator is
 * created by the person who installs the application:
 *
 *     php artisan app:create-admin
 *
 * The interns and the supervisors are then added from the interface, with
 * their own real information.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Information Technology', 'Human Resources', 'Finance', 'Marketing', 'Operations'] as $name) {
            Department::firstOrCreate(['name' => $name]);
        }

        $this->command?->info('Departments created. Now run: php artisan app:create-admin');
    }
}
