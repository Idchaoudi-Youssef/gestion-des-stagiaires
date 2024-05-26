<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'intern_id',
        'supervisor_id',
    ];

    /**
     * Get the intern that owns the task.
     */
    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    /**
     * Get the supervisor that assigned the task.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
