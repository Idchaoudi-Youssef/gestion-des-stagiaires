<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'usertype',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the tasks for the intern.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'intern_id');
    }

    /**
     * Get the reports for the intern.
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'intern_id');
    }

    /**
     * The supervisors that belong to the intern.
     */
    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'intern_supervisor', 'intern_id', 'supervisor_id');
    }

    /**
     * The interns that belong to the supervisor.
     */
    public function interns()
    {
        return $this->belongsToMany(User::class, 'intern_supervisor', 'supervisor_id', 'intern_id');
    }
}
