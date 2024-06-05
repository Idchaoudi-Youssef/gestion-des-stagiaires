<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'start_date',
        'end_date',
        'internship_period',
        'status',

    ];

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
