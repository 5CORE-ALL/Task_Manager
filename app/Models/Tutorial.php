<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_link',
        'thumbnail_image',
        'thumbnail_name',
        'menu_type',
    ];

    // Relation: a tutorial belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
