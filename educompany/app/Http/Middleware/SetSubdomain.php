<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SetSubdomain
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
        $url = $request->url();
        $urlParts = parse_url($url);
        $mainDomain = env('APP_DOMAIN');
        $host = $urlParts['host'];
        $subdomain=null;
        if (strpos($host, $mainDomain) !== false) {
            $subdomain = str_replace('.' . $mainDomain, '', $host);
            if($subdomain==env('APP_DOMAIN')){
                $subdomain=null;
            }
        }

        if($subdomain==null && Auth::guard('users')->check() && isset(Auth::guard('users')->user()->subdomain) && !empty(Auth::guard('users')->user()->subdomain)){
            $subdomain = Auth::guard('users')->user()->subdomain;
        }else{
            $subdomain = Session::get('subdomain')??null;
        }
        if($subdomain!=env('APP_DOMAIN')){
            Session::put('subdomain', $subdomain??null);
        }else{
            Session::put('subdomain', null);
        }
        return $next($request);
    }
}
