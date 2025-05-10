<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $role = Auth::user()->role->name;
        $is_allowed = false;
        foreach ($guards as $grd) {
            if ($role == $grd) $is_allowed = true;
        }
        if ($is_allowed) return $next($request);
        return redirect()->route('dashboard');
    }
}
