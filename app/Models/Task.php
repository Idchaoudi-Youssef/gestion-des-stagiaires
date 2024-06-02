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
        'state',
        'limit_date',
    ];

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
