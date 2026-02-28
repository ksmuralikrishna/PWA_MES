<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage in routes:
 *   ->middleware('module:receiving')
 *   ->middleware('module:receiving,can_create')
 */
class CheckModulePermission
{
    public function handle(Request $request, Closure $next, string $moduleSlug, string $action = 'can_view'): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['status' => 'error', 'message' => 'Account disabled.'], 403);
        }

        if (!$user->canAccessModule($moduleSlug, $action)) {
            return response()->json([
                'status'  => 'error',
                'message' => "You do not have {$action} access to the {$moduleSlug} module.",
            ], 403);
        }

        return $next($request);
    }
}
