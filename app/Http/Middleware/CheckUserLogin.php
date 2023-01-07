<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->user_type != 'user' || $request->user()->allow_login != 1) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
