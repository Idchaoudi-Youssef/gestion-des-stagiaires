<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternSupervisor extends Model
{
    use HasFactory;

    protected $table = 'intern_supervisor';

    protected $fillable = [
        'intern_id',
        'supervisor_id',
    ];

    /**
     * Get the intern associated with this relationship.
     */
    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    /**
     * Get the supervisor associated with this relationship.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
