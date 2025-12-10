<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PayrollAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated and has admin email
        if (!$user || !in_array(strtolower($user->email), ['president@5core.com', 'hr@5core.com', 'software2@5core.com', 'tech-support@5core.com', 'software13@5core.com'])) {
            // Redirect to salary slip page with error message
            return redirect()->route('payroll.salary-slip')
                ->with('error', 'Access denied. You do not have permission to manage payroll.');
        }
        
        return $next($request);
    }
}
