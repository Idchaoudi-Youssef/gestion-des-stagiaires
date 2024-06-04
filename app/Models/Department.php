<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // Add other fillable fields as needed
    ];

    public function internships()
    {
        return $this->hasMany(Internship::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
