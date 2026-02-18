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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('portal');
        }

        $userRole = str_replace('-', '_', Auth::user()->role);

        // If 'roles' contains a comma-separated string in the first element, split it
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }

        // Normalize allowed roles for comparison
        $normalizedRoles = array_map(fn($r) => str_replace('-', '_', trim($r)), $roles);

        if (!in_array($userRole, $normalizedRoles)) {
            // Avoid redirect loops: if we are already at the redirect destination, don't redirect again
            if ($request->routeIs('portal')) {
                Auth::logout();
                return $next($request);
            }
            return redirect()->route('portal')->withErrors(['usuario' => 'No tiene permisos para acceder a esta sección.']);
        }

        return $next($request);
    }
}
