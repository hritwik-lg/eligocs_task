<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuthenticate
{
    /**
     * Ensure the request is from an authenticated super admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('super_admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
