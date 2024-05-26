<?php

namespace App\Models;

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
    ];

    /**
     * Get the intern that owns the report.
     */
    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    /**
     * Get the supervisor associated with the report.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the admin associated with the report.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
