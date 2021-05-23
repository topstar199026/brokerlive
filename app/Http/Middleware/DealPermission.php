<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;

class DealPermission
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
        $userRole = Auth::user()->getUserRole();
        if($userRole->broker || $userRole->personalAssistant)
        {
            return $next($request);
        }
        else
        {
            return redirect('/dashboard');
        }
        
    }
}
