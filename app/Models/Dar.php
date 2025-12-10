<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_date',
        'total_time',
        'workspace_id',
        'created_by'
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_time' => 'decimal:2'
    ];

    /**
     * Get the user that owns the DAR.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks for the DAR.
     */
    public function tasks()
    {
        return $this->hasMany(DarTask::class);
    }

    /**
     * Get formatted total time
     */
    public function getFormattedTotalTimeAttribute()
    {
        $hours = floor($this->total_time / 60);
        $minutes = $this->total_time % 60;
        return "{$hours}h {$minutes}m";
    }
}
