<?php

namespace App\Http\Middleware\auth\authorization;

use App\Models\GroupMember;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isgroupMemberOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->tokenCan('role:admin')) {
            return $next($request);
        }

        $group = GroupMember::where('group_id', $request->route('group_id'))
            ->where('user_id', $user->id)
            ->first();
        if (!$group) {
            return response()->json([
                'status' => false,
                'message' => 'group not found',
            ], 403);
        }
        return $next($request);
    }
}
