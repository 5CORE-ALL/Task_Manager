<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTaskActivityReportAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $authorizedEmails = ['president@5core.com', 'tech-support@5core.com', 'inventory@5core.com'];
        
        if (!Auth::check() || !in_array(Auth::user()->email, $authorizedEmails)) {
            abort(403, 'You are not authorized to access this page. Only president@5core.com and tech-support@5core.com can view task activity reports.');
        }

        return $next($request);
    }
}
