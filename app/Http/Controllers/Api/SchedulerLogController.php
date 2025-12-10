<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchedulerLog;
use Illuminate\Http\Request;

class SchedulerLogController extends Controller
{
    public function index()
    {
        $logs = SchedulerLog::latest('id')->limit(100)->get();
        return response()->json($logs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'command'     => 'required|string',
            'status'      => 'required|string|in:running,success,failed,heartbeat',
            'started_at'  => 'nullable|date',
            'finished_at' => 'nullable|date',
            'runtime'     => 'nullable|numeric',
            'error'       => 'nullable|string',
            'meta'        => 'nullable|array',
        ]);

        // Save new log entry
        $log = SchedulerLog::create($validated);
        
         // --- If failed, send email alert using PHP mail() ---
    if ($log->status === 'failed') {
        // Recipient list
        $to = "tech-support@5core.com, software13@5core.com";

        // Subject
        $subject = "Scheduler Failed: {$log->command}";

        // HTML message
        $message = "
        <html>
        <head>
            <title>Scheduler Failure Alert</title>
        </head>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color:#c00;'>Scheduler Task Failed</h2>
            <p><strong>Command:</strong> {$log->command}</p>
            <p><strong>Status:</strong> {$log->status}</p>
            <p><strong>Finished At:</strong> {$log->finished_at}</p>
            <p><strong>Error:</strong> {$log->error}</p>
            <p style='margin-top:20px;'>Please investigate the issue.</p>
        </body>
        </html>
        ";

        // Headers
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: 5core TaskManager <admin@new-tm.5coremanagement.com>\r\n";
        $headers .= "Reply-To: admin@new-tm.5coremanagement.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Optional: ensure Return-Path is correct (helps deliverability)
        $additional_params = "-fadmin@new-tm.5coremanagement.com";

        // Attempt to send
        if (!mail($to, $subject, $message, $headers, $additional_params)) {
            \Log::warning("Scheduler failure email could not be sent for {$log->command}");
        }
    }

        return response()->json(['message' => 'OK', 'data' => $log]);
    }
}
