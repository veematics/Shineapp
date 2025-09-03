<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\FeatureAccess;
use Symfony\Component\HttpFoundation\Response;

class HierarchicalPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!FeatureAccess::hasPermissionHierarchical($permission)) {
            abort(403, 'This action is unauthorized.');
        }

        return $next($request);
    }
}