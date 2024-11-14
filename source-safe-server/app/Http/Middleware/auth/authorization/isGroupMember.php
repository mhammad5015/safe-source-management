<?php

namespace App\Http\Middleware\auth\authorization;

use App\Models\GroupMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isGroupMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_id = auth()->user()->id;
        $group = GroupMember::where('group_id', operator: $request->route('group_id'))->first();
        if (!$group) {
            return response()->json([
                'status' => false,
                'message' => 'group not found',
            ], 403);
        }
        $isGroupMember = GroupMember::where('group_id', operator: $request->route('group_id'))->where('user_id', $user_id)->first();
        if (!$isGroupMember) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to perform this action',
            ], 403);
        }
        return $next($request);
    }
}
