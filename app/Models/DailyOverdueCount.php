<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyOverdueCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'record_date',
        'overdue_count',
        'workspace',
    ];

    protected $casts = [
        'record_date' => 'date',
        'overdue_count' => 'integer',
    ];

    /**
     * Get the user that owns the daily overdue count.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}