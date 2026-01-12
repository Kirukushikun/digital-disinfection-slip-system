<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperGuard
{
    /**
     * Handle an incoming request.
     * Only allows super guards (user_type 0 with super_guard = true) or super admins (user_type 2)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Allow super guards OR super admins
        if (!($user->super_guard || $user->user_type === 2)) {
            // Regular guards trying to access super guard routes - redirect to landing
            return redirect('/')->with('status', 'You do not have permission to access this page.');
        }
        
        return $next($request);
    }
}
