<?php

namespace App\Console\Commands;

use App\Enums\UserType;
use App\Models\Department;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Creates the first administrator of the platform.
 *
 * The application ships without any account: the person who installs it
 * decides who the administrator is. Nothing is invented by the seeder.
 */
class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin
                            {--name= : The name of the administrator}
                            {--email= : The email address of the administrator}
                            {--password= : The password of the administrator}
                            {--department= : The department to attach the administrator to}';

    protected $description = 'Create the first administrator account';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email address');
        $password = $this->option('password') ?: $this->secret('Password');
        $departmentName = $this->option('department');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $department = null;

        if ($departmentName) {
            $department = Department::firstOrCreate(['name' => $departmentName]);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'usertype' => UserType::Admin->value,
            'email_verified_at' => now(),
            'department_id' => $department?->id,
        ]);

        $this->components->info("Administrator \"{$user->name}\" created with the address {$user->email}.");
        $this->line('  You can now sign in at '.config('app.url'));

        return self::SUCCESS;
    }
}
