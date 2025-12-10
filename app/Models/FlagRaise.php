<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlagRaise extends Model
{
    use HasFactory;

    protected $fillable = [
        'given_by',
        'team_member_id',
        'description',
        'flag_type',
    ];

    public function givenBy()
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    public function teamMember()
    {
        return $this->belongsTo(User::class, 'team_member_id');
    }
}
