<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    /**
     * Ensure the authenticated user matches one of the allowed user_type values.
     * Also check if user is disabled and block access if so.
     *
     * Special case: Superadmins (type 2) can access user routes (type 0) if they have a location in session.
     *
     * This middleware automatically determines the required user type based on the route prefix.
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $user = $request->user();
        $current = (int) optional($user)->user_type;

        // Automatically determine allowed user types based on route prefix
        $path = $request->path();
        $allowed = [];

        if (str_starts_with($path, 'user/')) {
            // User routes: allow guards (0) and superadmins with location (2)
            $allowed = [0];
        } elseif (str_starts_with($path, 'admin/')) {
            // Admin routes: allow admins (1)
            $allowed = [1];
        } elseif (str_starts_with($path, 'superadmin/')) {
            // Superadmin routes: allow superadmins (2)
            $allowed = [2];
        } else {
            // Default: allow all authenticated users
            $allowed = [0, 1, 2];
        }

        // Check if user type is allowed
        $isAllowed = in_array($current, $allowed, true);

        // Special case: Superadmins (type 2) can access user routes (type 0) if they have a location in session
        if (!$isAllowed && $current === 2 && in_array(0, $allowed, true)) {
            $hasLocation = $request->session()->has('location_id');
            if ($hasLocation) {
                $isAllowed = true;
            }
        }

        if (! $user || ! $isAllowed) {
            // Redirect to landing page instead of showing error
            return redirect('/')->with('status', 'You do not have permission to access this page.');
        }

        // Check if user is disabled - block access and logout
        if ($user->disabled) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('status', 'Your account has been disabled. Please contact an administrator.');
        }

        // Check if user is soft-deleted - block access and logout
        if ($user->trashed()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('status', 'Your account has been deleted. Please contact an administrator.');
        }

        return $next($request);
    }
}


