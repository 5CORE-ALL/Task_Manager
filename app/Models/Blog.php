<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blog_url',
        'featured_image',
        'title',
        'menu_type',
    ];

    // Relation: a blog belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

