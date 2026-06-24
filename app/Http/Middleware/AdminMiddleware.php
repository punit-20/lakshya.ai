<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Ensure the authenticated user has the 'admin' role.
     * Prevents client users from accessing admin-only routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            // If it's an AJAX request, return JSON 403
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized. Admin access required.'], 403);
            }

            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
