<?php

namespace App\Models;

use App\Enums\InternshipStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'start_date',
        'end_date',
        'internship_period',
        'position',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'internship_period' => 'integer',
            'status' => InternshipStatus::class,
        ];
    }

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }
}
