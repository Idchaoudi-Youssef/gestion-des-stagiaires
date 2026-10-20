<?php

namespace App\Models;

use App\Enums\ReportState;
use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'type',
        'intern_id',
        'supervisor_id',
        'admin_id',
        'state',
        'reject_reason',
        'file',
    ];

    protected function casts(): array
    {
        return [
            'type' => ReportType::class,
            'state' => ReportState::class,
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

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
