<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyTeam extends Model
{
    use HasFactory;
      protected $fillable = [
        'id',
        'tl_id',
        'team_id',
        'status',
    ];
}
