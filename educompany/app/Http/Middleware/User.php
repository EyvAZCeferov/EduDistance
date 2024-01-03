<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class User
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
        if (Auth::guard('users')->check()) {
            return $next($request);
        }else{
            $user_id=session()->get("user_id")??$request->user_id;
            $email=session()->get("email");
            if(isset($user_id) && !empty($user_id) && isset($email) && !empty($email)){
                Auth::guard('users')->loginUsingId($user_id);
                return $next($request);
            }else{
                return redirect()->route('login');
            }
        }
    }
}
