<?php

namespace App\Http\Middleware\auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->tokenCan('role:admin')) {
            return $next($request);
        }
        return response()->json(['message'=>'Not Authorized'], 401);
    }
}
