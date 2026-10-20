<?php

namespace App\Models;

use App\Enums\TaskState;
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
        'state',
        'limit_date',
    ];

    protected function casts(): array
    {
        return [
            'state' => TaskState::class,
            'limit_date' => 'datetime',
        ];
    }

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * A task is overdue when its deadline is in the past and it has not been
     * completed yet.
     */
    public function isOverdue(): bool
    {
        return $this->limit_date !== null
            && $this->limit_date->isPast()
            && $this->state !== TaskState::Completed;
    }
}
