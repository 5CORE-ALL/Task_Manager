<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamloggerTime extends Model
{
    protected $table = 'teamlogger_time';

    protected $fillable = [
        'email',
        'date',
        'activeHours',
        'total_hours',
        'idle_hours',
    ];
}
