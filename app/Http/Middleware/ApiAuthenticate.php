<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class ApiAuthenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if($token!=''&&$token!=null&&$token!='null'){
            $user = User::where('remember_token', $token)->first();
            if ($user) {
                auth()->login($user);
                return $next($request);
            }
        }
        return response([
            'message' => 'Unauthenticated'
        ], 403);
    }
}





