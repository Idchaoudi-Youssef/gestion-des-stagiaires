<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'usertype',
        'department_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'intern_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'intern_id');
    }

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'intern_supervisor', 'intern_id', 'supervisor_id');
    }

    public function interns()
    {
        return $this->belongsToMany(User::class, 'intern_supervisor', 'supervisor_id', 'intern_id');
    }
    public function internship()
    {
        return $this->hasOne(Internship::class , 'intern_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
