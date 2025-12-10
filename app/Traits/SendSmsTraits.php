<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendWhatsAppJob;

trait SendSmsTraits
{
      // inside your import model() or right after Task::create($task)
public function prepareAndQueueNotifications($task)
{
    // Collect all emails
    $assignEmails   = array_filter(array_map('trim', explode(',', $task->assign_to)));
    $assignerEmails = array_filter(array_map('trim', explode(',', $task->assignor)));

    $allEmails = array_values(array_unique(array_merge($assignEmails, $assignerEmails)));

    if (empty($allEmails)) {
        Log::warning("No emails found for task {$task->id}");
        return;
    }

    // Prefetch all users in ONE query
    $users = \App\Models\User::whereIn('email', $allEmails)
             ->select('id','email','name','mobile_no','last_whatsapp_reply_at')
             ->get()
             ->keyBy('email');

    // Build assigner name string from pre-fetched users
    $assignerNames = [];
    foreach ($assignerEmails as $email) {
        if (isset($users[$email])) $assignerNames[] = $users[$email]->name;
    }
    $assignerNameString = implode(', ', $assignerNames) ?: 'Team';

    // Prepare message payloads (no http calls here)
    $startDate = optional($task->start_date) ? date('d-m-Y', strtotime($task->start_date)) : '';
    $dueDate   = optional($task->due_date)   ? date('d-m-Y', strtotime($task->due_date)) : '';

    $payloads = [];
    foreach ($assignEmails as $email) {
        if (! isset($users[$email])) {
            Log::warning("No user found for assignee email: {$email}");
            continue;
        }
        $recipient = $users[$email];
        if (empty($recipient->mobile_no)) {
            Log::warning("Missing mobile_no for {$email}");
            continue;
        }

        $cleanNumber = preg_replace('/[^0-9]/','', $recipient->mobile_no);

        $sessionMessage = "Hello {$recipient->name},\n" .
            "A new task has been assigned by {$assignerNameString}.\n\n" .
            "📌 Task Name: {$task->title}\n\n" .
            "📅 Start Date: {$startDate}\n\n" .
            "⏰ Due Date: {$dueDate}\n\n" .
            "⏳ ETA: {$task->eta_time} minutes\n\n" .
            "Please complete it before the deadline.\n\n" .
            "Thank you,\nTeam";

        $within24h = $recipient->last_whatsapp_reply_at &&
                     \Carbon\Carbon::parse($recipient->last_whatsapp_reply_at)->gt(\Carbon\Carbon::now()->subHours(24));

        $payloads[] = [
            'task_id' => $task->id,
            'phone'   => $cleanNumber,
            'email'   => $recipient->email,
            'type'    => $within24h ? 'session' : 'template',
            'template_params' => [$recipient->name, $assignerNameString, $task->title, $startDate, $dueDate, $task->eta_time],
            'session_message' => $sessionMessage,
        ];
    }

    // Dispatch jobs (per recipient)
    foreach ($payloads as $p) {
        SendWhatsAppJob::dispatch($p)->onQueue('whatsapp');
    }
}
public function sendSms($task)
{
    try {
        $assign = explode(',', $task->assign_to);
        $assigners = explode(',', $task->assignor);
        
        $successCount = 0;

        // Get all assigners
        $assignerUsers = [];
        foreach ($assigners as $assignerEmail) {
            $assignerEmail = trim($assignerEmail);
            $assignerUser = User::where('email', $assignerEmail)->first();
            if ($assignerUser) {
                $assignerUsers[] = $assignerUser;
            }
        }

        if (empty($assignerUsers)) {
            Log::error('No assigners found');
            return false;
        }

        // Use the first assigner for the message (or you can modify to include all)
        $primaryAssigner = $assignerUsers[0];
        $assignerNames = array_map(function($user) {
            return $user->name;
        }, $assignerUsers);
        $assignerNameString = implode(', ', $assignerNames);

        $startDate = date('d-m-Y', strtotime($task->start_date));
        $dueDate   = date('d-m-Y', strtotime($task->due_date));

        // Loop through all assignees
        foreach ($assign as $assigneeEmail) {
            $assigneeEmail = trim($assigneeEmail);
            $recipient = User::where('email', $assigneeEmail)->first();

            if (!$recipient || empty($recipient->mobile_no)) {
                Log::error("Recipient not found or missing mobile number for email: {$assigneeEmail}");
                continue;
            }

            $cleanNumber = preg_replace('/[^0-9]/', '', $recipient->mobile_no);

            $sessionMessage = "Hello {$recipient->name},\n" .
                "A new task has been assigned by {$assignerNameString}.\n\n" .
                "📌 Task Name: {$task->title}\n\n" .
                "📅 Start Date: {$startDate}\n\n" .
                "⏰ Due Date: {$dueDate}\n\n" .
                "⏳ ETA: {$task->eta_time} minutes\n\n" .
                "Please complete it before the deadline.\n\n" .
                "Thank you,\n" .
                "Team 5Core";

            $lastReply = $recipient->last_whatsapp_reply_at ?? null;
            $within24h = $lastReply && Carbon::parse($lastReply)->gt(Carbon::now()->subHours(24));

            try {
                if ($within24h) {
                    $response = $this->sendGupshupMessage($cleanNumber, $sessionMessage);
                } else {
                    $templateParams = [
                        $recipient->name ?? 'Employee',
                        $assignerNameString,
                        $task->title,
                        $startDate,
                        $dueDate,
                        $task->eta_time
                    ];
                    $response = $this->sendGupshupTemplateMessage($cleanNumber, $templateParams);
                }

                if (isset($response['status']) && $response['status'] === 'submitted') {
                    $successCount++;
                }
            } catch (\Exception $e) {
                Log::error("WhatsApp notification error for {$assigneeEmail}: " . $e->getMessage());
            }
        }

        // Return true if at least one message was sent successfully
        return $successCount > 0;
    } catch (\Exception $e) {
        Log::error('WhatsApp notification error: ' . $e->getMessage());
        return false;
    }
}
    public function sendATCSms($task)
    {
        
        try {
            $assign = explode(',', $task->assign_to);
            $assigner = $task->assignor;

            $recipient = User::where('email', $assign[0])->first();
            $assignerUser = User::where('email', $assigner)->first();
            if (!$recipient || !$assignerUser || empty($assignerUser->mobile_no)) {
                Log::error('Recipient or assigner not found, or missing mobile number');
                return false;
            }
            

            // Clean mobile number (only digits)
            $cleanNumber = preg_replace('/[^0-9]/', '', $assignerUser->mobile_no);

            // Notification message
            $sessionMessage = "Hello {$assignerUser->name},\n\n" .
            "Your assign task is done by {$recipient->name}.\n\n" .
            "📌 Task Name: {$task->title}\n\n" .
            "📅 ETC: {$task->eta_time} Min\n\n" .
            "⏰ ATC: {$task->etc_done} Min\n\n" .
            "Hello Assignor, Please review the task and delete the task.\n\n" .
            "Thank you,\n" .
            "5Core Team";
            
           
            // Check if we can send session message (within 24h reply window)
            $lastReply = $assignerUser->last_whatsapp_reply_at ?? null;
            $within24h = $lastReply && Carbon::parse($lastReply)->gt(Carbon::now()->subHours(24));
            if ($within24h) {
                $response = $this->sendGupshupMessage($cleanNumber, $sessionMessage);
            } else {
                // Use template if outside 24h window
                $templateParams = [
                    $assignerUser->name ?? 'Manager',
                    $recipient->name ?? 'Employee',
                    $task->title,
                    $task->eta_time,
                    $task->etc_done
                ];
                $response = $this->sendGupshupTemplateDoneMessage($cleanNumber, $templateParams);
                 return $response;
            }

           
            return isset($response['status']) && $response['status'] === 'submitted';
        } catch (\Exception $e) {
            Log::error('WhatsApp notification error (Task Done): ' . $e->getMessage());
            return false;
        }
    }
    
    // rework message send to user
    public function sendATRSms($task)
    {
        try {
            $assign = explode(',', $task->assign_to);
            $assigner = $task->assignor;

            $recipient = User::where('email', $assign[0])->first();
            $assignerUser = User::where('email', $assigner)->first();
            if (!$recipient || !$assignerUser || empty($assignerUser->mobile_no)) {
                Log::error('Recipient or assigner not found, or missing mobile number');
                return false;
            }

            // Clean mobile number (only digits)
            $cleanNumber = preg_replace('/[^0-9]/', '', $recipient->mobile_no);

            // Notification message
            $sessionMessage = "Hello {$recipient->name},\n\n" .
            "Your assigned task has been marked as rework by {$assignerUser->name}.\n\n" .
            "📌 Task Name: {$task->title}\n\n" .
            "📅 ETC: {$task->eta_time} Min\n\n" .
            "⏰ ATC: {$task->etc_done} Min\n\n" .
            "Kindly review the task and take the necessary action. I've shared the task feedback below.\n\n" .
            "Note: {$task->rework_reason}.\n\n".
            "Thank you,\n" .
            "5Core Team";
            
             // Check if we can send session message (within 24h reply window)
            $lastReply = $recipient->last_whatsapp_reply_at ?? null;
            $within24h = $lastReply && Carbon::parse($lastReply)->gt(Carbon::now()->subHours(24));
            if ($within24h) {
                $response = $this->sendGupshupMessage($cleanNumber, $sessionMessage);
            } else {
                // Use template if outside 24h window
                $templateParams = [
                    $recipient->name ?? 'Employee',
                    $assignerUser->name ?? 'Manager',
                    $task->title,
                    $task->eta_time,
                    $task->etc_done,
                    $task->rework_reason
                ];
                $response = $this->sendGupshupTemplateReworkMessage($cleanNumber, $templateParams);
                 return $response;
            }

           
            return isset($response['status']) && $response['status'] === 'submitted';
        } catch (\Exception $e) {
            Log::error('WhatsApp notification error (Task Done): ' . $e->getMessage());
            return false;
        }
    }

public function sendTaskUpdateSms($task, $oldTaskData = null)
{
    try {
        Log::info('=== TASK UPDATE NOTIFICATION STARTED ===');
        Log::info('Task ID: ' . $task->id);
        Log::info('Task Title: ' . $task->title);

        $assign = explode(',', $task->assign_to);
        $assigners = explode(',', $task->assignor);
        
        Log::info('Assignees: ' . json_encode($assign));
        Log::info('Assigners: ' . json_encode($assigners));
        
        $successCount = 0;

        // Get all assigners
        $assignerUsers = [];
        foreach ($assigners as $assignerEmail) {
            $assignerEmail = trim($assignerEmail);
            $assignerUser = User::where('email', $assignerEmail)->first();
            if ($assignerUser) {
                $assignerUsers[] = $assignerUser;
            }
        }

        Log::info('Found assigner users: ' . count($assignerUsers));

        if (empty($assignerUsers)) {
            Log::error('No assigners found for task update notification');
            return false;
        }

        // Use the first assigner for the message
        $primaryAssigner = $assignerUsers[0];
        $assignerNames = array_map(function($user) {
            return $user->name;
        }, $assignerUsers);
        $assignerNameString = implode(', ', $assignerNames);

        $startDate = date('d-m-Y', strtotime($task->start_date));
        $dueDate   = date('d-m-Y', strtotime($task->due_date));

        Log::info('Starting loop through assignees');

        // Loop through all assignees
        foreach ($assign as $assigneeEmail) {
            $assigneeEmail = trim($assigneeEmail);
            Log::info('Processing assignee: ' . $assigneeEmail);
            
            $recipient = User::where('email', $assigneeEmail)->first();

            if (!$recipient) {
                Log::error("Recipient not found for email: {$assigneeEmail}");
                continue;
            }

            if (empty($recipient->mobile_no)) {
                Log::error("Missing mobile number for email: {$assigneeEmail}");
                continue;
            }

            $cleanNumber = preg_replace('/[^0-9]/', '', $recipient->mobile_no);
            Log::info('Clean mobile number: ' . $cleanNumber);

            $sessionMessage = "Hello {$recipient->name},\n\n" .
                "Your assigned task \"{$task->title}\" has been updated by {$assignerNameString}.\n\n" .
                "📌 Task Name: {$task->title}\n\n" .
                "📅 Start Date: {$startDate}\n\n" .
                "⏰ Due Date: {$dueDate}\n\n" .
                "⏳ ETA: {$task->eta_time} minutes\n\n" .
                "🚫 Please review the changes at your earliest convenience. If you have any concerns, kindly reach out to your assignor directly.\n\n" .
                "Thank you,\n" .
                "Team 5Core";

            $lastReply = $recipient->last_whatsapp_reply_at ?? null;
            $within24h = $lastReply && Carbon::parse($lastReply)->gt(Carbon::now()->subHours(24));
            
            Log::info('Within 24h window: ' . ($within24h ? 'YES' : 'NO'));

            try {
                if ($within24h) {
                    Log::info('Sending session message');
                    $response = $this->sendGupshupMessage($cleanNumber, $sessionMessage);
                } else {
                    Log::info('Sending template message');
                    $templateParams = [
                        $recipient->name ?? 'Employee',
                        $assignerNameString,
                        $task->title,
                        $startDate,
                        $dueDate,
                        $task->eta_time . ' minutes'
                    ];
                    Log::info('Template params: ' . json_encode($templateParams));
                    $response = $this->sendGupshupTemplateUpdateMessage($cleanNumber, $templateParams);
                }

                Log::info('Gupshup response: ' . json_encode($response));

                if (isset($response['status']) && $response['status'] === 'submitted') {
                    $successCount++;
                    Log::info("✅ Task update notification sent successfully to {$assigneeEmail}");
                } else {
                    Log::error("❌ Failed to send task update notification to {$assigneeEmail}: " . json_encode($response));
                }
            } catch (\Exception $e) {
                Log::error("🔥 Task update notification error for {$assigneeEmail}: " . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }

        Log::info('=== TASK UPDATE NOTIFICATION COMPLETED === Success count: ' . $successCount);
        return $successCount > 0;
    } catch (\Exception $e) {
        Log::error('🔥 MAJOR ERROR in task update notification: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        return false;
    }
}
    protected function sendGupshupMessage($number, $message)
    {

        $payload = [
            'channel'     => 'whatsapp',
            'source'      => env('GUPSHUP_SOURCE'),
            'destination' => $number,
            'src.name'    => env('GUPSHUP_SRC_NAME'),
            'message'     => json_encode([
                'type' => 'text',
                'text' => $message
            ]),
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.gupshup.io/wa/api/v1/msg',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_HTTPHEADER     => [
                'apikey: ' . env('GUPSHUP_API_KEY'),
                'Content-Type: application/x-www-form-urlencoded'
            ],
        ]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return ['status' => 'failed', 'error' => $error];
        }
        curl_close($curl);

        return json_decode($response, true) ?? ['status' => 'failed'];
    }

    protected function sendGupshupTemplateMessage($number, array $params)
    {
        $url = "https://api.gupshup.io/wa/api/v1/template/msg";

        $postFields = http_build_query([
            'channel'     => 'whatsapp',
            'source'      => env('GUPSHUP_SOURCE'),
            'destination' => $number,
            'src.name'    => env('GUPSHUP_SRC_NAME'),
            'template'    => json_encode([
                'id'     => env('GUPSHUP_TEMPLATE_ID'),
                'params' => $params
            ])
        ]);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . env('GUPSHUP_API_KEY'),
            "Content-Type: application/x-www-form-urlencoded",
            "Cache-Control: no-cache"
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 'failed', 'error' => $error];
        }

        return json_decode($response, true);
    }
    protected function sendGupshupTemplateDoneMessage($number, array $params)
    {
        $url = "https://api.gupshup.io/wa/api/v1/template/msg";

        $postFields = http_build_query([
            'channel'     => 'whatsapp',
            'source'      => env('GUPSHUP_SOURCE'),
            'destination' => $number,
            'src.name'    => env('GUPSHUP_SRC_NAME'),
            'template'    => json_encode([
                'id'     => env('GUPSHUP_TEMPLATE_ID_DONE_TASK'),
                'params' => $params
            ])
        ]);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . env('GUPSHUP_API_KEY'),
            "Content-Type: application/x-www-form-urlencoded",
            "Cache-Control: no-cache"
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 'failed', 'error' => $error];
        }

        return json_decode($response, true);
    }
    
     protected function sendGupshupTemplateReworkMessage($number, array $params)
    {
        $url = "https://api.gupshup.io/wa/api/v1/template/msg";

        $postFields = http_build_query([
            'channel'     => 'whatsapp',
            'source'      => env('GUPSHUP_SOURCE'),
            'destination' => $number,
            'src.name'    => env('GUPSHUP_SRC_NAME'),
            'template'    => json_encode([
                'id'     => env('GUPSHUP_TEMPLATE_ID_REWORK_TASK'),
                'params' => $params
            ])
        ]);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: " . env('GUPSHUP_API_KEY'),
            "Content-Type: application/x-www-form-urlencoded",
            "Cache-Control: no-cache"
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 'failed', 'error' => $error];
        }

        return json_decode($response, true);
    }
  protected function sendGupshupTemplateUpdateMessage($number, array $params)
{
    try {
        Log::info('Sending Gupshup template message to: ' . $number);
        Log::info('Template params: ' . json_encode($params));
        
        $url = "https://api.gupshup.io/wa/api/v1/template/msg";

        // Use the hardcoded template ID for task updates
        $templateId = 'fa0fd4ec-fdf8-4174-803e-000f3de275fb';
        
        Log::info('Using template ID: ' . $templateId);

        $postFields = http_build_query([
            'channel'     => 'whatsapp',
            'source'      => env('GUPSHUP_SOURCE'),
            'destination' => $number,
            'src.name'    => env('GUPSHUP_SRC_NAME'),
            'template'    => json_encode([
                'id'     => env('GUPSHUP_TEMPLATE_ID_UPDATE_TASK'), // Use the hardcoded template ID
                'params' => $params
            ])
        ]);

        Log::info('Gupshup API Payload: ' . $postFields);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                "apikey: " . env('GUPSHUP_API_KEY'),
                "Content-Type: application/x-www-form-urlencoded",
                "Cache-Control: no-cache"
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        Log::info('Gupshup HTTP Code: ' . $httpCode);
        Log::info('Gupshup Response: ' . $response);

        if ($error) {
            Log::error("Gupshup template message error: " . $error);
            return ['status' => 'failed', 'error' => $error, 'http_code' => $httpCode];
        }

        $decodedResponse = json_decode($response, true) ?? $response;
        return $decodedResponse;

    } catch (\Exception $e) {
        Log::error('Exception in sendGupshupTemplateUpdateMessage: ' . $e->getMessage());
        return ['status' => 'failed', 'error' => $e->getMessage()];
    }
}
}
