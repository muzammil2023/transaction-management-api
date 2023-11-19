<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user is an admin
        if ($request->user() && $request->user()->role == 'admin') {
            return $next($request);
        }

        // If not an admin, you can customize the response or redirect
        abort(403, 'Unauthorized');
    }
}
