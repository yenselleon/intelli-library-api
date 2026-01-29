<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->user() || auth()->user()->role !== $role) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Forbidden'
            ], 403);
        }

        return $next($request);
    }
}
