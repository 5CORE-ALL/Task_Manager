<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle($lang = null)
    {
        // $lang = $lang ?: 'en';
        return Socialite::driver('google')->redirect();
    }


    /**
     * Handle Google OAuth callback
     */

    
    public function handleGoogleCallback($lang = null)
    {
        try {
            $lang = $lang ?: 'en';
            
            // Debug logging
            Log::info('Google callback hit', ['lang' => $lang, 'request' => request()->all()]);
            
            $googleUser = Socialite::driver('google')->user();
            
            // Check if email domain is 5core.com
            if (!$this->isValidDomain($googleUser->getEmail())) {
                return redirect()->route('login', $lang)->withErrors([
                    'email' => 'Only @5core.com email addresses are allowed to login with Google.'
                ]);
            }

            // Check if user exists in database
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                return redirect()->route('login', $lang)->withErrors([
                    'email' => 'Your email address is not registered in our system. Please contact administrator.'
                ]);
            }

            // Update user's Google ID if not set
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId()
                ]);
            }

            // Log in the user
            Auth::login($user, true);

            return redirect('/dashboard/taskly');

        } catch (\Exception $e) {
            Log::error('Google auth error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('login', $lang)->withErrors([
                'email' => 'An error occurred during Google authentication. Please try again.'
            ]);
        }
    }

    /**
     * Check if email domain is valid (5core.com)
     */

    private function isValidDomain($email)
    {
        // Whitelist of special Gmail IDs (lowercase for consistent comparison)
        $allowedSpecialEmails = [
            'sjoy7486@gmail.com',
            'ritu.kaur013@gmail.com',
            'jadhavharshit66@gmail.com',
            'sneha.workplace@gmail.com',
            'ghosharitrika52@gmail.com',
            'oasis101007@gmail.com',
            'chakrabortysougata96@gmail.com',
            'iaminchina2@gmail.com',
            'roytoreto007@gmail.com',
        ];
    
        $email = strtolower($email);
        $domain = substr(strrchr($email, "@"), 1);
    
        // Allow if domain is 5core.com OR email is in special list
        return $domain === '5core.com' || in_array($email, $allowedSpecialEmails);
    }
}
