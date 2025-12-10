<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskActivityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'activity_type',
        'user_name',
        'user_email',
        'ip_address',
        'activity_date',
        'details',
        'is_deleted'
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'is_deleted' => 'boolean'
    ];

    public static function logActivity($taskName, $activityType, $userName, $userEmail, $ipAddress, $details = null)
    {
        return self::create([
            'task_name' => $taskName,
            'activity_type' => $activityType,
            'user_name' => $userName,
            'user_email' => $userEmail,
            'ip_address' => $ipAddress,
            'activity_date' => now(),
            'details' => $details
        ]);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeForAuthorizedUsers($query)
    {
        $authorizedEmails = ['president@5core.com', 'tech-support@5core.com', 'inventory@5core.com'];
        return $query->whereIn('user_email', $authorizedEmails);
    }
}
