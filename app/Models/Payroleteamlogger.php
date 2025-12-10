<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroleteamlogger extends Model
{
    protected $table = 'payroleteamlogger';

    protected $fillable = [
        'email_address',
        'month',
        'productive_hrs',
        'approved_hrs',
    ];
}
