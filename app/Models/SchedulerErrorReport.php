<?php
// app/Models/SchedulerErrorReport.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulerErrorReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_issue_found',
        'issue_found_and_fixed',
        'corrective_action_applied',
        'remarks',
    ];
}
