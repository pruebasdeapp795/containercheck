<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles, $guard = 'web'): Response
    {
        if (!Auth::guard($guard)->check()) {
            return redirect('/');
        }

        $userRole = Auth::guard($guard)->user()->role;
        $allowedRoles = explode(',', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            return redirect('/');
        }

        return $next($request);
    }
}
