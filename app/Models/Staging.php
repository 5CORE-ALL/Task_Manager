<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staging extends Model
{
    use HasFactory; 

    protected $fillable = [
        'user_id',
        'event',
        'event_note',
        'status',
    ];

    public function tasks()
    {
        return $this->hasMany(StagingTask::class, 'event_id');
    }
}
