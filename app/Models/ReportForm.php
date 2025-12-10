<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_name',
        'category',
        'status',
        'frequency',
        'owner_name',
        'assignee',
        'form_link',
        'report_link',
        'enable_form_view',
        'enable_report_view',
    ];
}
