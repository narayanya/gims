<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super-admin bypasses all permission checks
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden', 'message' => "You don't have permission: {$permission}"], 403);
            }
            abort(403, "You don't have permission to access this page. Required: {$permission}");
        }

        return $next($request);
    }
}
