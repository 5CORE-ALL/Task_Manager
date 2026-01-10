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
            
            // Check if user exists in database and is visible/active in /users view
            $user = $this->getValidUserForGoogleLogin($googleUser->getEmail());
            
            if (!$user) {
                return redirect()->route('login', $lang)->withErrors([
                    'email' => 'Your email address is not registered or you do not have permission to login with Google. Please contact administrator.'
                ]);
            }

            // Update user's Google ID if not set
            if (!$user->google_id) {
                try {
                    $user->update([
                        'google_id' => $googleUser->getId()
                    ]);
                } catch (\Exception $e) {
                    // If google_id column doesn't exist, log the error but continue with login
                    Log::warning('Could not update google_id for user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
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
     * Check if user exists and is visible/active in /users view
     * Only users that would be visible in the /users view should be allowed to login
     * 
     * @param string $email
     * @return User|null
     */
    private function getValidUserForGoogleLogin($email)
    {
        // Find user by email
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            // User doesn't exist (possibly deleted)
            return null;
        }

        // Super admins should not use Google login
        if ($user->type === 'super admin') {
            return null;
        }

        // Check if user is active (is_disable = 1 means active/enabled)
        // Users with is_disable = 0 are disabled and should not appear in /users view
        if ($user->is_disable != 1) {
            return null;
        }

        // Check if login is enabled for this user
        // Users with is_enable_login = 0 have login disabled
        if ($user->is_enable_login != 1) {
            return null;
        }

        // User must be visible in /users view, which means:
        // - For non-super admin users: they must have a creator (created_by is set)
        // - User must be active (is_disable = 1) and login enabled (is_enable_login = 1)
        // Since we already checked is_disable and is_enable_login above,
        // we just need to ensure the user would be visible in someone's view
        
        // At this point, if user exists, is active, has login enabled, and is not super admin,
        // they would be visible in the /users view (either in super admin's view or their creator's view)
        
        return $user;
    }
}
