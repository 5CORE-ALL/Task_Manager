<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyShippingChecklist extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_name',
        'checklist_date',
        'task_1',
        'task_1_comments',
        'task_2',
        'task_2_comments',
        'task_3',
        'task_3_comments',
        'task_4',
        'task_4_comments',
        'user_id'
    ];
    
    protected $dates = ['checklist_date'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
