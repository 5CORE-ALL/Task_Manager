<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTaskManagerApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-TASKMANAGER-KEY');
        if (!$key || $key !== env('TASKMANAGER_API_KEY')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
