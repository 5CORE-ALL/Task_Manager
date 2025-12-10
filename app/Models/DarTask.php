<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'dar_id',
        'group_name',
        'description',
        'time_spent',
        'status'
    ];

    protected $casts = [
        'time_spent' => 'integer'
    ];

    /**
     * Get the DAR that owns the task.
     */
    public function dar()
    {
        return $this->belongsTo(Dar::class);
    }

    /**
     * Get formatted time spent
     */
    public function getFormattedTimeSpentAttribute()
    {
        return $this->time_spent . ' min';
    }
}
