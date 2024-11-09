<?php

namespace App\Http\Middleware\auth\authorization;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $group_id = $request->route('group_id'); // get group_id from the route
        $user = auth()->user();
        $group = Group::find($group_id);
        if (!$group) {
            return response()->json([
                'status' => false,
                'message' => 'group not found',
            ], 403);
        }
        if ($group->owner_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to perform this action',
            ], 403);
        }
        return $next($request);
    }
}
