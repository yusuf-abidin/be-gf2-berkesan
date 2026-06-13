<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        if (!in_array($request->user()->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Anda tidak memiliki akses ke halaman ini.'
            ], 403);
        }
        return $next($request);
    }
}
