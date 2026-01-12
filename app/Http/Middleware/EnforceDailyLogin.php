<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceDailyLogin
{
    /**
     * Handle an incoming request.
     * 
     * Forces users to login by checking if 12 hours have passed since login.
     * Sessions expire after 12 hours regardless of activity.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for non-authenticated users or login/logout routes
        if (!Auth::check() || 
            $request->routeIs('login') || 
            $request->routeIs('logout') || 
            $request->routeIs('password.request') ||
            $request->routeIs('password.reset') ||
            $request->routeIs('password.update') ||
            $request->routeIs('impersonate') ||
            $request->routeIs('stop.impersonate')) {
            return $next($request);
        }

        $session = $request->session();
        $loginTimestamp = $session->get('login_timestamp');

        // If no login timestamp exists, force logout
        // This handles users who logged in before this feature was added
        if (!$loginTimestamp) {
            // If user is impersonating, stop impersonation first
            if (Auth::user() && method_exists(Auth::user(), 'isImpersonating') && Auth::user()->isImpersonating()) {
                Auth::user()->leaveImpersonation();
            }
            
            Auth::logout();
            $session->invalidate();
            $session->regenerateToken();

            return redirect()->route('login')
                ->with('error', __('Your session has expired. Please login again to continue.'));
        }

        // Calculate hours since login
        $hoursSinceLogin = (now()->timestamp - $loginTimestamp) / 3600;

        // Force logout if 12 hours have passed since login
        if ($hoursSinceLogin >= 12) {
            // If user is impersonating, stop impersonation first
            if (Auth::user() && method_exists(Auth::user(), 'isImpersonating') && Auth::user()->isImpersonating()) {
                Auth::user()->leaveImpersonation();
            }
            
            Auth::logout();
            $session->invalidate();
            $session->regenerateToken();

            return redirect()->route('login')
                ->with('error', __('Your session has expired. Please login again to continue.'));
        }

        return $next($request);
    }
}
