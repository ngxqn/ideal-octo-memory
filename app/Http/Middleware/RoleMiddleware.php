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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Require auth before checking rules
        if (!$request->user()) {
            throw new \Illuminate\Auth\AuthenticationException('Unauthenticated.');
        }

        if ($request->user()->role !== $role) {
            abort(403, 'Forbidden. This action is unauthorized.');
        }

        return $next($request);
    }
}
