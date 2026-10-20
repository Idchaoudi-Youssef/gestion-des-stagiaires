<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'usertype',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'usertype' => UserType::class,
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'intern_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'intern_id');
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'intern_supervisor', 'intern_id', 'supervisor_id');
    }

    public function interns(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'intern_supervisor', 'supervisor_id', 'intern_id');
    }

    public function internship(): HasOne
    {
        return $this->hasOne(Internship::class, 'intern_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * The dashboard the user has to be sent to after signing in.
     */
    public function homeRoute(): string
    {
        return match ($this->usertype) {
            UserType::Admin => 'admin.dashboard',
            UserType::Supervisor => 'supervisor.dashboard',
            UserType::Intern => 'intern.dashboard',
            default => 'pending',
        };
    }
}
