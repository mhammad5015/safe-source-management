<?php

namespace App\Http\Middleware\auth\authorization;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isNotBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find(auth()->user()->id);
        if ($user['isBlocked'] == 1) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to perform this action, you are blocked',
            ], 403);
        }
        return $next($request);
    }
}
