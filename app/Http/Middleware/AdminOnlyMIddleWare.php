<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class AdminOnlyMIddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin' || $user->role !== 'sub_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
                'data' => ['error' => 'This process is for the admin only']
            ], 403);
        }

        return $next($request);
    }
}
