<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailAfter30Days extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-welcome-email-after-30days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send welcome email to users after 30 days of joining';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Get users who joined exactly 30 days ago
            $thirtyDaysAgo = Carbon::now()->subDays(30)->startOfDay();
            $thirtyDaysAgoEnd = Carbon::now()->subDays(30)->endOfDay();

            $users = User::whereBetween('created_at', [$thirtyDaysAgo, $thirtyDaysAgoEnd])
                ->whereNotNull('email')
                ->get();
            
            // $users = User::where('email', 'software13@5core.com')->get();

            $this->info("Found " . $users->count() . " users who joined 30 days ago.");

            foreach ($users as $user) {
                try {
                    // Prepare email content
                    $to = $user->email;
                    $subject = "30-Day Onboarding & Training - Feedback Form";
                    
                    $formLink = "https://docs.google.com/forms/d/e/1FAIpQLSfryXGWON2qLOwjA_6U6G6WiUWcaASDsdb4AMKks0HhNNCKBw/viewform"; // Update with actual form URL
                    
                    $message = "
                    <html>
                    <head>
                      <title>30-Day Feedback & Training Form</title>
                    </head>
                    <body style=\"font-family: Arial, sans-serif; line-height:1.6; color:#333;\">
                        <p>👋 Hi " . htmlspecialchars($user->name) . ",</p>

                        <p>Congratulations on completing 30 days with us! 🎉<br>
                        We hope your first month has been productive and rewarding.</p>

                        <p>To help us improve your experience and ensure you have everything you need, please take a few minutes to complete the following form:</p>

                        <p>
                            👉 <a href=\"" . $formLink . "\" 
                            style=\"display:inline-block; padding:12px 20px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:8px;\">
                            Click here to fill out the 30-day feedback form</a>
                        </p>

                        <p>Your feedback is valuable to us and will help us continue to support your growth and success within the organization.</p>

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
                        Log::info("30-day welcome email sent to user: " . $user->email);
                    } else {
                        $this->error("Failed to send email to: " . $user->email);
                        Log::error("Failed to send 30-day welcome email to user: " . $user->email);
                    }
                } catch (\Exception $e) {
                    $this->error("Failed to send email to: " . $user->email);
                    Log::error("Failed to send 30-day welcome email to user: " . $user->email . " - Error: " . $e->getMessage());
                }
            }

            $this->info("30-day welcome email scheduler completed successfully.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error in scheduler: " . $e->getMessage());
            Log::error("Error in SendWelcomeEmailAfter30Days scheduler: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
