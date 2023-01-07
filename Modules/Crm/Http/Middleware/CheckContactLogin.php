<?php

namespace Modules\Crm\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckContactLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->user_type != 'user_customer') {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}
