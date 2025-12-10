<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailAfter7Days extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-welcome-email-after-7days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send welcome email to users after 7 days of joining';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Get users who joined exactly 7 days ago
            $sevenDaysAgo = Carbon::now()->subDays(7)->startOfDay();
            $sevenDaysAgoEnd = Carbon::now()->subDays(7)->endOfDay();

            $users = User::whereBetween('created_at', [$sevenDaysAgo, $sevenDaysAgoEnd])
                ->whereNotNull('email')
                ->get();
            // $users = User::where('email', 'software13@5core.com')->get();

            $this->info("Found " . $users->count() . " users who joined 7 days ago.");

            foreach ($users as $user) {
                try {
                    // Prepare email content
                    $to = $user->email;
                    $subject = "7-Day Onboarding & TrainingFeedback & Training Form";
                    
                    $formLink = "https://docs.google.com/forms/d/e/1FAIpQLSfqJjU9SHvNWAsMrk0SSKFd_INseq1HGjfNZFmJjCC0IwqSzw/viewform"; // Update with actual form URL
                    
                    $message = "
                    <html>
                    <head>
                      <title>Feedback & Training Form</title>
                    </head>
                    <body style=\"font-family: Arial, sans-serif; line-height:1.6; color:#333;\">
                        <p>👋 Hi " . htmlspecialchars($user->name) . ",</p>

                        <p>We hope you're doing well.<br>
                        To help us keep everything up to date, please take a few minutes to complete the following form:</p>

                        <p>
                            👉 <a href=\"" . $formLink . "\" 
                            style=\"display:inline-block; padding:12px 20px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:8px;\">
                            Click here to fill out the form</a>
                        </p>

                        <p>Your timely response is important. Thank you for your cooperation.</p>

                        <p style=\"color:red; font-weight:bold;\">⚠️ Note: This is a system-generated email. Please do not reply to this message.</p>

                        <br>
                        <p>Best regards,<br>
                        <strong>5core HR Team</strong></p>
                    </body>
                    </html>
                    ";
                    
                    // Set headers
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $headers .= "From: 5Core HR Team <admin@new-tm.5coremanagement.com>\r\n";
                    $headers .= "Reply-To: admin@new-tm.5coremanagement.com\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();
                    
                    // Send email
                    if (mail($to, $subject, $message, $headers)) {
                        $this->info("Email sent to: " . $user->email);
                        Log::info("7-day welcome email sent to user: " . $user->email);
                    } else {
                        $this->error("Failed to send email to: " . $user->email);
                        Log::error("Failed to send 7-day welcome email to user: " . $user->email);
                    }
                } catch (\Exception $e) {
                    $this->error("Failed to send email to: " . $user->email);
                    Log::error("Failed to send 7-day welcome email to user: " . $user->email . " - Error: " . $e->getMessage());
                }
            }

            $this->info("7-day welcome email scheduler completed successfully.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error in scheduler: " . $e->getMessage());
            Log::error("Error in SendWelcomeEmailAfter7Days scheduler: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
