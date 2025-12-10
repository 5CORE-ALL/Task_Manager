<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ExitInterviewMail;

class ExitInterviewController extends Controller
{
    /**
     * Display the exit interview form.
     */
    public function index()
    {
        return view('exitinterview.exitinterview');
    }

    /**
     * Handle the exit interview form submission.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $email = $request->email;
            $formLink = 'https://docs.google.com/forms/d/e/1FAIpQLSczsUidaIR3YKIezxPz7SyIaaI0kHVq7NgM9ndUNByWEjT47Q/viewform';
            
            // Send email using the ExitInterviewMail class
            Mail::to($email)->send(new ExitInterviewMail($formLink, $email));

            // Check for failures
            if (count(Mail::failures()) > 0) {
                throw new \Exception('Email failed to send');
            }

            return response()->json([
                'success' => true,
                'message' => 'Exit interview form link has been sent to your email successfully! Please check your inbox and spam folder.'
            ]);

        } catch (\Exception $e) {
            Log::error('Exit Interview Email Error: ' . $e->getMessage());
            Log::error('Email attempted: ' . $email);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again or contact HR directly.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
}